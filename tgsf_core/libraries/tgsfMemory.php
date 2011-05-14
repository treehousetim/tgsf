<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
/**
* This simple array tracks created tgsfBase objects.  It does not reference the objects but rather
* tracks by name how many of a given object are still in memory.  This way leaks can be detected
*
*/

class tgsfMemory
{
	private static $enableTracking = false;
	private static $instances = array();
	private static $instances2 = array();


	static function echoLog()
	{
		echo "\r\n\r\nObjects in Memory:\r\n";

		foreach( self::$instances as $name => $count )
		{
			//if ( $count )
			{

				if ( array_key_exists( $name, self::$instances2) )
				{
					if ( $count != self::$instances2[$name] )
					{
						echo "changed";
					}
					else
					{
						echo "same   ";
					}
				}
				else
				{
					echo "new    ";
				}

				echo "  " . $count ."  " . $name . "\r\n";
			}
		}
		self::$instances2 = self::$instances;
	}

	static function newInstance( $class )
	{
		$className = get_class($class);

		if ( array_key_exists( $className, self::$instances) )
		{
			self::$instances[$className] = self::$instances[$className] + 1;
		}
		else
		{
			self::$instances[$className] = 1;
		}
	}

	static function removeInstance( $class )
	{
		$className = get_class($class);

		if ( array_key_exists( $className, self::$instances) )
		{
			self::$instances[$className] = self::$instances[$className] - 1;
		}
		else
		{
//			echo 'destruct ' . $className . ' not counted in __construct' . "\r\n";
		}

	}

	static function echoUnchecked()
	{
		$classes[] = 'logNoteModel';
		$classes[] = 'logModel';
		$classes[] = 'tgsfDbRegistry';
		$classes[] = 'wizardScheduleStartDateForm';
		$classes[] = 'ACHDirectFileProcessing';
		$classes[] = 'ACHDirectFiles';
		$classes[] = 'ManualTxnReportForm';
		$classes[] = 'wizardScheduleFrequencyForm';
		$classes[] = 'achAddendaRecord';
		$classes[] = 'achBatchControlRecord';
		$classes[] = 'achBatchHeaderRecord';
		$classes[] = 'achDetailRecord';
		$classes[] = 'achFileControlRecord';
		$classes[] = 'achFileHeaderRecord';
		$classes[] = 'achRecordBuilder';
		$classes[] = 'arbItemPadLeft';
		$classes[] = 'arbItemPadRight';
		$classes[] = 'tgsfGridGroupFooterCell';
		$classes[] = 'simpleMemberSearchForm';
		$classes[] = 'txn_log_chargeback_completed_reporttgsfReport';
		$classes[] = 'withdrawForm';
		$classes[] = 'wizardExternalAccountForm';
		$classes[] = 'accountStatementSearchForm';
		$classes[] = 'achRecordFactory';
		$classes[] = 'admin_bankAccountsGrid';
		$classes[] = 'arbItemFactory';
		$classes[] = 'depositForm';
		$classes[] = 'tvr_alpha';
		$classes[] = 'tvr_alpha_numeric';
		$classes[] = 'tvr_alphanum_extended';
		$classes[] = 'tvr_bank_routing';
		$classes[] = 'tvr_clean';
		$classes[] = 'tvr_clean_address';
		$classes[] = 'tvr_clean_question';
		$classes[] = 'tvr_credit_card';
		$classes[] = 'tvr_custom';
		$classes[] = 'tvr_date';
		$classes[] = 'tvr_db_exists';
		$classes[] = 'tvr_db_unique';
		$classes[] = 'tvr_email';
		$classes[] = 'tvr_future_date';
		$classes[] = 'tvr_gt';
		$classes[] = 'tvr_gte';
		$classes[] = 'tvr_int';
		$classes[] = 'tvr_lt';
		$classes[] = 'tvr_lte';
		$classes[] = 'tvr_lte_float';
		$classes[] = 'tvr_match_field';
		$classes[] = 'tvr_match_value';
		$classes[] = 'tvr_max_len';
		$classes[] = 'tvr_min_len';
		$classes[] = 'tvr_neq';
		$classes[] = 'tvr_not_match_field';
		$classes[] = 'tvr_numeric';
		$classes[] = 'tvr_required';
		$classes[] = 'tvr_usa_canada_state';
		$classes[] = 'tvr_usa_phone';
		$classes[] = 'tvr_usa_state';
		$classes[] = 'tvr_usa_zipcode';
		$classes[] = 'wizardScheduleAmountForm';
		$classes[] = 'mmFormTop';
		$classes[] = 'tgsfValidateField';
		$classes[] = 'accountHistorySearchForm';
		$classes[] = 'allRecordsSignup';
		$classes[] = 'loginMailForm';
		$classes[] = 'loginMailForm_Phone';
		$classes[] = 'memberAffiliatePercent';
		$classes[] = 'externalAccountEditForm';
		$classes[] = 'fixedLengthFieldParser';
		$classes[] = 'loginMailForm_Phone';
		$classes[] = 'productExchangeCostGrid';
		$classes[] = 'tgsfParseFilterInt';
		$classes[] = 'tgsfParseFilterTrim';
		$classes[] = 'unconfirmed_bankaccounts_report';
		$classes[] = 'externalAccountForm';
		$classes[] = 'fixedLengthFieldDefinition';
		$classes[] = 'storedReportNamesGrid';
		$classes[] = 'tgsfParseFilterFactory';
		$classes[] = 'tgsfPluginLoader';
		$classes[] = 'tgsfSearchEngineDetect';
		$classes[] = 'memberSuspendForm';
		$classes[] = 'staticPage';
		$classes[] = 'tgsfValidate';
		$classes[] = 'OutputFormatterCLI';
		$classes[] = 'memberSearchForm';
		$classes[] = 'processSellListForm';
		$classes[] = 'sellListGrid';
		$classes[] = 'setPasswordForm';
		$classes[] = 'tgsfPaginateQuery';
		$classes[] = 'userSecurityQuestionForm';
		$classes[] = 'OutputColumn';
		$classes[] = 'achFileBuilderModel';
		$classes[] = 'mmAffiliatePlugin';
		$classes[] = 'tgsfBreadcrumb';
		$classes[] = 'tgsfBreadcrumbItem';
		$classes[] = 'tgsfLog';
		$classes[] = 'bankAccountsGrid';
		$classes[] = 'companyAccountsGrid';
		$classes[] = 'dateSearchForm';
		$classes[] = 'externalAccountForm';
		$classes[] = 'formTop';
		$classes[] = 'tgsfGridGroup';
		$classes[] = 'TxnManual';
		$classes[] = 'accountSummaryGrid';
		$classes[] = 'adjustmentForm';
		$classes[] = 'changeUsernameForm';
		$classes[] = 'marketResultsGrid';
		$classes[] = 'tgsfAction';
		$classes[] = 'tgsfFilter';
		$classes[] = 'tgsfHandler';
		$classes[] = 'txnManualForm';
		$classes[] = 'txn_log_chargeback_report';
		$classes[] = 'upcoming_schedules_report';
		$classes[] = 'userChangePasswordForm';
		$classes[] = 'xbatchReportForm';
		$classes[] = 'xbatch_records_grid';
		$classes[] = 'account_statementReport';
		$classes[] = 'achFileTest';
		$classes[] = 'dateSearchForm';
		$classes[] = 'marketPricePublicGrid';
		$classes[] = 'parserFactory';
		$classes[] = 'pendingSellTxnsGrid';
		$classes[] = 'specialDateForm';
		$classes[] = 'specialDateGrid';
		$classes[] = 'storedReportGrid';
		$classes[] = 'tgsfEvent';
		$classes[] = 'tgsfEventFactory';
		$classes[] = 'tgsfFormField';
		$classes[] = 'tgsfHtmlTag';
		$classes[] = 'tgsfPlugin';
		$classes[] = 'txnSellListForm';
		$classes[] = 'wizardLoginForm';
		$classes[] = 'ACHDirect';
		$classes[] = 'ACHInterfaceTest';
		$classes[] = 'ACHMessage';
		$classes[] = 'ACHResponse';
		$classes[] = 'ACHTransaction';
		$classes[] = 'accountTypeForm';
		$classes[] = 'accountTypeGrid';
		$classes[] = 'entityToAccountModel';
		$classes[] = 'externalAccountModel';
		$classes[] = 'marketPriceForm';
		$classes[] = 'marketPriceGrid';
		$classes[] = 'sellCurrencyForm';
		$classes[] = 'tgsfGridCol';
		$classes[] = 'tgsfGridRowHeader';
		$classes[] = 'txn_log_rejected_report';
		$classes[] = 'changeUsernameForm';
		$classes[] = 'companyTxnGrid';
		$classes[] = 'dbDataSource';
		$classes[] = 'demoSetup';
		$classes[] = 'sellForm';
		$classes[] = 'staticPageModel';
		$classes[] = 'tgsfDataSource';
		$classes[] = 'txnSearch';
		$classes[] = 'achTxnLogModel';
		$classes[] = 'dsFactory';
		$classes[] = 'foreignKey';
		$classes[] = 'loginMailGrid';
		$classes[] = 'rteForm';
		$classes[] = 'tgsfUserAuthCLI';
		$classes[] = 'ACHResultsForm';
		$classes[] = 'activateForm';
		$classes[] = 'dbManager';
		$classes[] = 'newShipmentReport';
		$classes[] = 'tgsfUserAuth';
		$classes[] = 'txnReport';
		$classes[] = 'ACHInterface';
		$classes[] = 'achBatchModel';
		$classes[] = 'achFileModel';
		$classes[] = 'loginSearch';
		$classes[] = 'newSignupReport';
		$classes[] = 'queryJoin';
		$classes[] = 'shipmentsGrid';
		$classes[] = 'specialDateModel';
		$classes[] = 'statementGrid';
		$classes[] = 'storageFeeReport';
		$classes[] = 'storedReportModel';
		$classes[] = 'tgsfSession';
		$classes[] = 'url_marketing_model';
		$classes[] = 'accountTypeModel';
		$classes[] = 'achTxnModel';
		$classes[] = 'dbIndex';
		$classes[] = 'dbIndexCol';
		$classes[] = 'marketPriceModel';
		$classes[] = 'member_dump_report';
		$classes[] = 'microdepositModel';
		$classes[] = 'productForm';
		$classes[] = 'productGrid';
		$classes[] = 'scheduleForm';
		$classes[] = 'scheduleGrid';
		$classes[] = 'tgsfFormat';
		$classes[] = 'tgsfIDD';
		$classes[] = 'tgsfMemory';
		$classes[] = 'timezoneForm';
		$classes[] = 'ACHTestForm';
		$classes[] = 'SilverTxnReport';
		$classes[] = 'accountsGrid';
		$classes[] = 'appException';
		$classes[] = 'dbSetup';
		$classes[] = 'login_batchModel';
		$classes[] = 'passwordReset2';
		$classes[] = 'productGrid';
		$classes[] = 'tgsfCrypt';
		$classes[] = 'tgsfDbException';
		$classes[] = 'tgsfException';
		$classes[] = 'tgsfFormException';
		$classes[] = 'tgsfGridException';
		$classes[] = 'tgsfHtmlException';
		$classes[] = 'tgsfValidationException';
		$classes[] = 'userAccountForm';
		$classes[] = 'DailyNetReport';
		$classes[] = 'entityForm';
		$classes[] = 'entityGrid';
		$classes[] = 'loginMail';
		$classes[] = 'passwordResetForm';
		$classes[] = 'query';
		$classes[] = 'queryParam';
		$classes[] = 'signupForm';
		$classes[] = 'table';
		$classes[] = 'tgsfCLIOutput';
		$classes[] = 'tgsfHTMLOutput';
		$classes[] = 'tgsfPost';
		$classes[] = 'tgsfSFTP';
		$classes[] = 'tgsfTest';
		$classes[] = 'date';
		$classes[] = 'field';
		$classes[] = 'logSeverityForm';
		$classes[] = 'loginForm';
		$classes[] = 'model';
		$classes[] = 'tgsfCli';
		$classes[] = 'tgsfGet';
		$classes[] = 'tgsfUrl';
		$classes[] = 'txn_batch_model';
		$classes[] = 'currencyModel';
		$classes[] = 'scheduleModel';
		$classes[] = 'sellForm';
		$classes[] = 'accountModel';
		$classes[] = 'achTxnModel';
		$classes[] = 'productModel';
		$classes[] = 'txnGrid';
		$classes[] = 'txn_logModel';
		$classes[] = 'entityModel';
		$classes[] = 'xbatch_model';
		$classes[] = 'batchModel';
		$classes[] = 'logNoteForm';
		$classes[] = 'loginModel';
		$classes[] = 'shipModel';
		$classes[] = 'txnModel';
		$classes[] = 'tzModel';
		$classes[] = 'exampleGrid';
		$classes[] = 'logListGrid';


		foreach ( $classes as $class )
		{
			if ( array_key_exists( $class, self::$instances) === false )
			{
				echo $class . " never instantiated" . PHP_EOL;
			}

		}
	}

};
