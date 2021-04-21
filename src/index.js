/**
 * External dependencies
 */

import { addFilter } from '@wordpress/hooks';
import { __ } from '@wordpress/i18n';
//
// /**
//  * Add a dropdown to a report.
//  *
//  * @param {Array} filters - set of filters in a report.
//  * @return {Array} amended set of filters.
//  */
const addVendorFilters = ( filters ) => {
	return [
		{
			label: __( 'Vendor', 'plugin-domain' ),
			staticParams: [],
			param: 'vendor',
			showFilters: () => true,
			defaultValue: 'all',
			filters: [ ...( wcSettings.vendors || [] ) ],
		},
		...filters,
	];

};
// addFilter(
// 	'woocommerce_admin_revenue_report_filters',
// 	'plugin-domain',
// 	addVendorFilters
// );
// addFilter(
// 	'woocommerce_admin_orders_report_filters',
// 	'plugin-domain',
// 	addVendorFilters
// );
// addFilter(
// 	'woocommerce_admin_products_report_filters',
// 	'plugin-domain',
// 	addVendorFilters
// );
// addFilter(
// 	'woocommerce_admin_categories_report_filters',
// 	'plugin-domain',
// 	addVendorFilters
// );
// addFilter(
// 	'woocommerce_admin_coupons_report_filters',
// 	'plugin-domain',
// 	addVendorFilters
// );
addFilter(
	'woocommerce_admin_taxes_report_filters',
	'plugin-domain',
	addVendorFilters
);
// addFilter(
// 	'woocommerce_admin_dashboard_filters',
// 	'plugin-domain',
// 	addVendorFilters
// );
//
//
//
// /**
//  * Add a column to a report table. Include a header and
//  * manipulate each row to handle the added parameter.
//  *
//  * @param {Object} reportTableData - table data.
//  * @return {Object} - table data.
//  */
// const addTableColumn = ( reportTableData ) => {
// 	const includedReports = [
// 		'revenue',
// 		'products',
// 		'orders',
// 		'categories',
// 		'coupons',
// 		'taxes',
// 	];
// 	if ( ! includedReports.includes( reportTableData.endpoint ) ) {
// 		return reportTableData;
// 	}
//
// 	const newHeaders = [
// 		{
// 			label: 'Vendor',
// 			key: 'vendor',
// 		},
// 		...reportTableData.headers,
// 	];
// 	const newRows = reportTableData.rows.map( ( row, index ) => {
// 		const item = reportTableData.items.data[ index ];
// 		const vendor =
// 			reportTableData.endpoint === 'revenue'
// 				? item.subtotals.vendor
// 				: item.vendor;
// 		const newRow = [
// 			{
// 				display: vendor,
// 				value: vendor,
// 			},
// 			...row,
// 		];
// 		return newRow;
// 	} );
//
// 	reportTableData.headers = newHeaders;
// 	reportTableData.rows = newRows;
//
// 	return reportTableData;
// };
//
// addFilter( 'woocommerce_admin_report_table', 'plugin-domain', addTableColumn );
//
//
// /**
//  * Add 'currency' to the list of persisted queries so that the parameter remains
//  * when navigating from report to report.
//  *
//  * @param {Array} params - array of report slugs.
//  * @return {Array} - array of report slugs including 'currency'.
//  */
// const persistQueries = ( params ) => {
// 	params.push( 'vendor' );
// 	return params;
// };
//
// addFilter(
// 	'woocommerce_admin_persisted_queries',
// 	'plugin-domain',
// 	persistQueries
// );
