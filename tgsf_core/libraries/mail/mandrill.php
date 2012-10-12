<?php defined( 'BASEPATH' ) or die( 'Restricted' );

load_library( 'tgsfDataSet', IS_CORE );
/*
-----------------------------------------------------------------------------
| This file is copyright 2012 by TMLA INC ALL RIGHTS RESERVED.
|----------------------------------------------------------------------------
| A mailer for mandrillapp.com - utilizing their HTTP semi-restful api
-----------------------------------------------------------------------------
*/

//------------------------------------------------------------------------
// A setup dataset
//------------------------------------------------------------------------
class mandrillMailSetup extends tgsfDataSet
{
	protected function _setup()
	{
		$this->define( 'key' )->type( 'string' );
		$this->define( 'type' )
			->type( 'string' )
			->whiteList( 'mandrill', 'nomail', 'noemail','no_email' );
	}
}
//------------------------------------------------------------------------
// holds an email address
//------------------------------------------------------------------------
class mandrillEmail extends tgsfDataSet
{
	protected function _setup()
	{
		$this->define( 'email' );
		$this->define( 'name' );
	}
	//------------------------------------------------------------------------
	public function clean()
	{
		$this->email( clean_for_email( $this->email ) );
		$this->name( clean_for_email( $this->name ) );
	}
	//------------------------------------------------------------------------
	public function __toString()
	{
		return $this->name . '<' . $this->email . '>';
	}
	//------------------------------------------------------------------------
	public function &setValue( $name, $value )
	{
		$value = clean_for_email( $value );
		return parent::setValue( $name, $value );
	}
}
//------------------------------------------------------------------------
// the mailer class
//------------------------------------------------------------------------
class mandrillMail extends tgsfDataSet
{
	protected function _setup()
	{
		$this->define( 'setup' )->type( 'mandrillMailSetup' );

		$this->define( 'to' )->type( 'array' );
		$this->define( 'from' )->type( 'mandrillEmail' );

		$this->define( 'subject' );
		$this->define( 'message' );
		$this->define( 'template' );

		$this->define( 'template_vars' )
			->type( 'tgsfDataSource' );

		$this->define( 'track_opens' )
			->type( 'boolean' );

		$this->define( 'track_clicks' )
			->type( 'boolean' );

		$this->define( 'auto_text' )
			->type( 'boolean' );

		$this->define( 'errors' )
			->type( 'array' );

		$this->define( 'tags' )
			->type( 'array' );

		// defaults
		$this
			->track_opens( true )
			->track_clicks( true )
			->auto_text( true )
			->errors( array() )
			->tags( array() )
			->template_vars( dsFactory::ds() );
	}
	//------------------------------------------------------------------------
	public function to( $to = null )
	{
		if ( $to == null )
		{
			$to = new mandrillEmail();
			$to->parent = $this;
		}

		if ( ( is_array( $to ) || $to instanceOf mandrillEmail ) == false )
		{
			throw new tgsfException( 'supplied argument is not of type mandrillEmail' );
		}

		if ( is_array( $to ) )
		{
			foreach( $to as $info )
			{
				if ( ! $info instanceOf mandrillEmail )
				{
					throw new tgsfException( 'supplied argument is not of type mandrillEmail' );
				}

				$this->push( 'to', $info );
			}
		}
		else
		{
			$this->push( 'to', $to );
		}

		return $to;
	}
	//------------------------------------------------------------------------
	public function getToArray()
	{
		$out = array();

		foreach( $this->to as $to )
		{
			$out[] = array( "email" => $to->email, 'name' => $to->name );
		}

		return $out;
	}
	//------------------------------------------------------------------------
	function send()
	{
		$retVal = false;

		// no mail when we're running in demo mode or if the type is not mandrill
		if ( inDemoMode() || $this->setup->type != 'mandrill' )
		{
			return;
		}

		$this->from->clean();

		try
		{
			$message = array
			(
				'key' => $this->setup->key,
				'message' => array
				(
					"from_email" => $this->from->email,
					"from_name" => $this->from->name,
					"subject" => $this->subject,
					"to" => $this->getToArray(),
					"track_opens" => $this->track_opens,
					"track_clicks" => $this->track_clicks,
					"auto_text" => $this->auto_text
				)
			);

			if ( count( $this->tags ) )
			{
				$message['message']['tags'] = $this->tags;
			}

			if ( $this->template == '' )
			{
				$handler = 'send.php';
				$message['message']['html'] = $this->message;
			}
			else
			{
				$handler = 'send-template.php';
				$message['template_name'] = $this->template;

				$vars = $this->template_vars->dataArray();
				$message['template_content'][] = array();
				foreach( $vars as $name => $value )
				{
					$message['message']['global_merge_vars'][] = array( 'name' => $name, 'content' => $value );
				}
			}

			$curl = curl_init( 'https://mandrillapp.com/api/1.0/messages/' . $handler );

			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true );

			curl_setopt( $curl, CURLOPT_HEADER, true );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

			curl_setopt($curl, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );

			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode( $message ) );

			//------------------------------------------------------------------------
			$response = curl_exec( $curl );
			$responseCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

			if ( $responseCode != 200 )
			{
				throw new tgsfException( 'Mandrill API Url returned ' . $responseCode . ' - ' . $response . PHP_EOL . get_dump( $message ) );
			}

			curl_close( $curl );

			if ( $response == false )
			{
				throw new tgsfException( 'Mandrill API Connection Error: ' . curl_error( $curl ) );
			}

			// split up headers and response text
			list( $headers, $response ) = explode( "\r\n\r\n", $response , 2);
			// mandrill is a bit odd...
			if( strpos( $headers," 100 Continue" )!== false )
			{
				list( $headers, $response) = explode( "\r\n\r\n", $response , 2 );
			}

			// we requested a php response type
			$responseAr = unserialize( trim( $response ) );

			// if there are multiple receiptents, each one could fail
			if ( count( $responseAr ) )
			{
				foreach( $responseAr as $individualResponse )
				{
					if ( $individualResponse['status'] == 'rejected' || $individualResponse['status'] == 'invalid' )
					{
						$this->push( 'errors', $individualResponse['email'] . ' was ' . $individualResponse['status'] );
					}
				}
			}

			if ( count( $this->errors ) )
			{
				// throwing an exception here may not be the best plan... we'll see
				throw new tgsfException( implode( "\n", $this->errors ) );
			}

			$retVal = true;
		}
		catch( Exception $e )
		{
			LOGGER()->exception( $e, 'Problem sending mail' . PHP_EOL . $e->getMessage(), 'mail' );
		}

		return $retVal;
	}
}