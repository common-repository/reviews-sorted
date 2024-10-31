<?php
	$verify_key = get_option('reviews_sorted_verify_key', '');
    $response   = get_option('reviews_sorted_verify_data', []);
?>
<style>
.list{list-style-type: lower-alpha;}
</style>
<div class="wrap">
	<div style="clear: both;"></div>
	<hr class="wp-header-end">
	<h1 class="wp-heading-inline"><?php _e('Premium License Key', 'reviews-sorted'); ?></h1>

	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label for="rs-licence_key"><?php _e('License Key.', 'reviews-sorted'); ?></label></th>
				<th>
					<input required type="password" class="regular-text" id="rs-licence_key" name="licence_key" placeholder="Enter license key" value="<?php echo $verify_key; ?>">
					<?php if($verify_key): ?>
				 	<p style="font-weight:normal; "><?php _e('Your license key is <strong>ACTIVE</strong> and your account level is <strong style="color: green;">PRO</strong>', 'reviews-sorted'); ?></p>
				 	<?php else: ?>
				 	<p style="font-weight:normal; "><?php _e('Please visit <a href="www.reviewssorted.com">www.reviewssorted.com</a> to set up your premium account.', 'reviews-sorted'); ?></p>
				 	<?php endif; ?>
				</th>
				<td style="vertical-align: top;">
					<p style="padding-top: 5px;margin-top: 0" class="submit">
						<?php if(!$verify_key): ?>
						<input type="button" id="verify-key" class="button button-primary" value="Verify Key">
						<?php else: ?>
						<input type="button" id="deactivate-key" class="button button-secondary" value="Deactivate Key" style="margin-left: 10px;">
						<?php endif; ?>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<p>Reviews Sorted has been created to help you manage your online reputation, boost your customer reviews and share positive customer feedback to prospective customers.</p>
	<p>We also connect and monitor your online reputation on social media, thanks to social listening, letting you know every time someone mentions your business in the social space.  You’ll never miss an opportunity to be apart of the conversation with Reviews Sorted social listening.</p>
	<p>Unlock more reviews and customer referrals with our automated premium platform. Automate customer referral requests and upload customer lists and request reviews via email or SMS with customised templates.</p>
	<p>Connect and manage all of your reviews on one simple Dashboard. View and reply in real time:</p>
	<ol class="list">
	<li>Google Reviews</li>
	<li>Facebook Recommends</li>
	<li>Facebook & Twitter Mentions</li>
	</ol>
	<p>Visit <u>www.reviewssorted.com</u> and upgrade today!</p>
</div>
<script type="text/javascript">
	const admin_ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
	<?php if($verify_key): ?>
	const btnDeactivate = document.getElementById('deactivate-key');
	btnDeactivate.addEventListener("click", function(){
		let ajax_data = new FormData(); 
			ajax_data.append('deactivate', 'true');
			ajax_data.append('licence', '');
			ajax_data.append('action', 'reviews_sorted_verify_key');

		fetch(admin_ajax_url + '?action=reviews_sorted_verify_key', {
		  	method	: 'POST',
		  	body	: ajax_data
		}).then(response => {
			location.href = location.href;
		});
	});
	<?php endif; ?>

	<?php if(!$verify_key): ?>
	const btnVerify 	= document.getElementById('verify-key');
	btnVerify.addEventListener("click", function(){
		
		let headers = new Headers();
	    let licence = document.getElementById('rs-licence_key').value;
	    if( licence.length == 0 ){
	    	alert('Please enter a value!');
	    }
	    else{
			
			let ajax_data = new FormData(); 			
			ajax_data.append('licence', licence);
			ajax_data.append('action', 'reviews_sorted_verify_key');

			fetch(admin_ajax_url + '?action=reviews_sorted_verify_key', {
			  	method	: 'POST',
			  	body	: ajax_data
			}).then(response => response.json())
			.then(response => {
				if(response.success){
					location.href = location.href;
				}
				else{
					alert(response.data);
				}
			}).catch((error) => {
			  	console.error('Error:', error);
			});
		}
	});
	<?php endif; ?>
</script>