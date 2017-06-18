<?php
/**
 * Plugin Name: Bonuin Package Payments
 * Plugin URI: http://tomplabs.com
 * Description: This plugin adds option to add Invoices in backend for Bonuin Theme.
 * Version: 1.0.0
 * Author: Can Mehmet
 * Author URI: http://tomplabs.com/can
 * License: GPL2
 */
function bonuin_invoices_menu_initiate() {
    add_menu_page(
        'Package Invoices',
        'Package Invoices',
        'manage_options',
        'bonuin_package_invoices',
        'bonuin_invoice_view' , get_template_directory_uri().'/assets/img/bonuin_admin_icon.png' , 5 );
}
add_action('admin_menu', 'bonuin_invoices_menu_initiate');	
	


add_action( 'init', 'bonuin_invoice_call_scripts' );
function bonuin_invoice_call_scripts() {
	// and can manage options (Admins)
	
	if( current_user_can( 'manage_options' ) ) {
		 if (is_admin()){
			 // Get the Path to this plugin's folder
				$path = plugin_dir_url( __FILE__ );

				
				wp_register_style( 'bonuin_invoices_style', $path . 'css/custom.css' );
				
				wp_register_style( 'bonuin_invoices_remodal', $path . 'css/remodal/remodal.css' );
				wp_register_style( 'bonuin_invoices_remodal_theme', $path . 'css/remodal/remodal-default-theme.css' );
				
				wp_enqueue_style( 'bonuin_invoices_style' );
				wp_enqueue_style( 'bonuin_invoices_remodal' );
				wp_enqueue_style( 'bonuin_invoices_remodal_theme' );
				
				
				// Enqueue our script
				wp_enqueue_script( 'bonuin_invoices_remodal_script',$path. 'js/remodal/remodal.min.js',array( 'jquery' ),'1.0.0', true );
				wp_enqueue_script( 'bonuin_invoices_table_script',$path. 'js/bootstrap-table.min.js',array( 'jquery' ),'1.0.0', true );
				wp_enqueue_script( 'bonuin_invoices_script',$path. 'js/init.js',array( 'jquery' ),'1.0.0', true );

				// Get the protocol of the current page
				$protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';

				// Set the ajaxurl Parameter which will be output right before
				// our ajax-delete-posts.js file so we can use ajaxurl
				$params = array(
					// Get the url to the admin-ajax.php file using admin_url()
					'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
				);
				// Print the script to our page
				wp_localize_script( 'bonuin_invoices_script', 'bonuin_params', $params );
			 
		 }
		
	}
}


	function bonuin_invoice_view(){
		//enqueu_admin_styles();
		
		?>
		<style>
		
</style>
		<div class="makes-and-models-wrapper">
			<h1>Package Invoices</h1>
			
			<div class="fullwidth">
				
				<div id="bonuin_loader"><img src="<?php echo plugin_dir_url( __FILE__ ) .'img/preloader.gif';?>"></div>
				<div class="search_table">
					<form>	
						<input type="text" placeholder="Transaction ID" id="bonuin_search_invoice" name="bonuin_search_invoice">
					</form>
					
				
				</div>
				<div id="xhani"></div>
				<table class="bonuin_admin_table" id="invoice_listings">
					
					<thead>
						<tr>
							<th>Transaction ID</th>
							<th>Status</th>
							<th>Name</th>
							<th>Surname</th>
							<th>Email</th>
							<th>Package Id</th>
							<th>Package Name</th>
							<th>Amount</th>
							<th>Date</th>
						</tr>
					
					</thead>
					<tbody>
						<?php bonuin_invoice_get_invoices();?>
					</tbody>
				</table>
				</div>
			</div>
			
		</div>
		
		
		<?php 
	
		
	}
	
	function bonuin_count_invoices(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'bonuin_package_payments';
		
		$results = $wpdb->get_results( 'SELECT COUNT(*) as counter FROM '.$table_name.'');
		echo $results[0]['counter'];		
	}
	
	function bonuin_search_transaction(){
			if(isset($_POST['transaction_id'])){
				$transaction_id_search = $_POST['transaction_id'];
				global $wpdb;
				
				$table_name = $wpdb->prefix . 'bonuin_package_payments';
				
				$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE invoice_transaction_id LIKE '%".$transaction_id_search."%' ORDER BY invoice_inserted_date DESC LIMIT 15"  );
				
			
				foreach ( $results as $invoice ) {
						?>
						<?php 
						$status_color = '';
							$payment_status = $invoice->invoice_payment_status;
							$payment_status = strtolower($payment_status);
							switch ($payment_status) {
								case 'completed':
									$status_color = 'green';
								break;
								case 'pending':
									$status_color = 'blue';
								break;
							}
						?>
						<tr data-invoice="<?php echo $invoice->invoice_id;?>">
							<td class="bold"><?php echo $invoice->invoice_transaction_id;?></td>
							<td class="<?php echo $status_color;?> bold"><?php echo $invoice->invoice_payment_status;?></td>
							<td><?php echo $invoice->invoice_payee_name;?></td>
							<td><?php echo $invoice->invoice_payee_surname;?></td>
							<td><?php echo $invoice->invoice_payee_email;?></td>
							<td><?php echo $invoice->invoice_item_id;?></td>
							<td><?php echo $invoice->invoice_item_name;?></td>
							<td><?php echo $payment_status;?><?php echo $invoice->invoice_payment_currency;?></td>
							<td><?php echo $invoice->invoice_inserted_date;?></td>
						
						</tr>
						
						<?php 
					}
					die();
			
			}else{
				return 'Type a transaction_id';
				die();
			}
		
	}
	add_action('wp_ajax_bonuin_search_transaction','bonuin_search_transaction');
	
	function bonuin_invoice_get_invoices(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'bonuin_package_payments';
		
		$results = $wpdb->get_results( 'SELECT * FROM '.$table_name.' ORDER BY invoice_inserted_date DESC LIMIT 15');
		
		if (!empty($results)){
			foreach ( $results as $invoice ) {
				$status_color = '';
				$payment_status = $invoice->invoice_payment_status;
				$payment_status = strtolower($payment_status);
				switch ($payment_status) {
					case 'completed':
					    $status_color = 'green';
					break;
					case 'pending':
					    $status_color = 'blue';
					break;
				}
				?>
				
				<tr data-invoice="<?php echo $invoice->invoice_id;?>">
					<td class="bold"><?php echo $invoice->invoice_transaction_id;?></td>
					<td class="<?php echo $status_color;?> bold"><?php echo $payment_status;?></td>
					<td><?php echo $invoice->invoice_payee_name;?></td>
					<td><?php echo $invoice->invoice_payee_surname;?></td>
					<td><?php echo $invoice->invoice_payee_email;?></td>
					<td><?php echo $invoice->invoice_item_id;?></td>
					<td><?php echo $invoice->invoice_item_name;?></td>
					<td><?php echo $invoice->invoice_payment_amount;?><?php echo $invoice->invoice_payment_currency;?></td>
					<td><?php echo $invoice->invoice_inserted_date;?></td>
				
				</tr>
				
				<?php 
			}
		}else{
			_e('There are no invoices right now','bonuin_theme');
			
		}
		
		
		?>
		
					
					<?php 
	}
?>