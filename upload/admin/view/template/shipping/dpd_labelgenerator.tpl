<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><img src="view/image/shipping.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
    </div>
    <div class="content">
			<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
				<?php if( !$dpd_carrier_installed ) { ?></h1>
					<h2>Delis Credentials</h2>
					<table class="form">
						<tr>
							<td><span class="required">*</span> <?php echo $delis_id; ?></td>
							<td><input type="text" name="dpd_labelgenerator_delis_id" cols="40" value="<?php echo $dpd_labelgenerator_delis_id; ?>">
								<?php if ($delis_id_error) { ?>
								<span class="error"><?php echo $delis_id_error; ?></span>
								<?php } ?></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $delis_password; ?></td>
							<td><input type="password" name="dpd_labelgenerator_delis_password" cols="40" value="<?php echo $dpd_labelgenerator_delis_password; ?>">
								<?php if ($delis_password_error) { ?>
								<span class="error"><?php echo $delis_password_error; ?></span>
								<?php } ?></td>
						</tr>
						<tr>
							<td><span class="required">*</span> <?php echo $delis_server; ?></td>
							<td>
								<input type="radio" name="dpd_labelgenerator_delis_server" value="1" <?php echo $dpd_labelgenerator_delis_server ? "checked" : ""; ?>><?php echo $delis_server_live; ?><br>
								<input type="radio" name="dpd_labelgenerator_delis_server" value="0" <?php echo $dpd_labelgenerator_delis_server ? "" : "checked"; ?>><?php echo $delis_server_stage; ?>
								<?php if ($delis_server_error) { ?>
								<span class="error"><?php echo $delis_server_error; ?></span>
								<?php } ?></td>
							</td>
						</tr>
					</table>
				<?php } ?>
				<h2>Shipping Details</h2>
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $shipper_name1; ?></td>
            <td><input type="text" name="dpd_labelgenerator_shipper_name1" cols="40" value="<?php echo $dpd_labelgenerator_shipper_name1; ?>">
              <?php if ($shipper_name1_error) { ?>
              <span class="error"><?php echo $shipper_name1_error; ?></span>
              <?php } ?></td>
          </tr>
					<tr>
            <td><span class="required">*</span> <?php echo $shipper_street . ' / ' . $shipper_houseNo; ?></td>
            <td><input type="text" name="dpd_labelgenerator_shipper_street" cols="40" value="<?php echo $dpd_labelgenerator_shipper_street; ?>">
							<input type="text" name="dpd_labelgenerator_shipper_houseNo" cols="9" value="<?php echo $dpd_labelgenerator_shipper_houseNo; ?>">
              <?php if ($shipper_street_error) { ?>
              <span class="error"><?php echo $shipper_street_error; ?></span>
              <?php } ?></td>
							<?php if ($shipper_houseNo_error) { ?>
              <span class="error"><?php echo $shipper_houseNo_error; ?></span>
              <?php } ?></td>
          </tr>
					<tr>
            <td><span class="required">*</span> <?php echo $shipper_country; ?></td>
            <td><input type="text" name="dpd_labelgenerator_shipper_country" cols="40" value="<?php echo $dpd_labelgenerator_shipper_country; ?>">
              <?php if ($shipper_country_error) { ?>
              <span class="error"><?php echo $shipper_country_error; ?></span>
              <?php } ?></td>
          </tr>
					<tr>
            <td><span class="required">*</span> <?php echo $shipper_PC; ?></td>
            <td><input type="text" name="dpd_labelgenerator_shipper_PC" cols="40" value="<?php echo $dpd_labelgenerator_shipper_PC; ?>">
              <?php if ($shipper_PC_error) { ?>
              <span class="error"><?php echo $shipper_PC_error; ?></span>
              <?php } ?></td>
          </tr>
					<tr>
            <td><span class="required">*</span> <?php echo $shipper_city; ?></td>
            <td><input type="text" name="dpd_labelgenerator_shipper_city" cols="40" value="<?php echo $dpd_labelgenerator_shipper_city; ?>">
              <?php if ($shipper_city_error) { ?>
              <span class="error"><?php echo $shipper_city_error; ?></span>
              <?php } ?></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
</div>
<?php echo $footer; ?>