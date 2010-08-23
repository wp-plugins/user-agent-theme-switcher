<?php
    if($debug == 'true') {
	$debug = 'checked="checked"';
    } else {
	$debug = '';
    }
?>
<div class="wrap">
    <h2>Navegadores</h2>
    <table class="widefat page fixed" width="100%" cellpadding="3" cellspacing="3">
	<thead>
	    <tr>
		<th class="manage-column" scope="col"></th>
		<th class="manage-column" scope="col">Navegador</th>
		<th class="manage-column" scope="col">Tema</th>
		<th class="manage-column" scope="col">Borrar</th>
	    </tr>
	</thead>
	<tbody>
	    <?php
		for($i = 0; $i < count($browsers); $i++) {
		    echo '<tr><td>*</td><td>'.$browsers[$i]->name.'</td><td>'.$browsers[$i]->theme.'</td><td><form method="post" action="'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=useragent-template"><input type="hidden" name="usts_action" value="deletebrowser" /><input type="hidden" name="browser" value="'.$browsers[$i]->id.'" /><input type="submit" value="borrar" class="button bold" /></form></td></tr>';
		}
	    ?>
	</tbody>
    </table>
    <h2>Nuevo navegador</h2>
    <form method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=useragent-template">
	<input type="hidden" name="usts_action" value="addbrowser" />
	<div class="postbox">
	    <div style="margin: 7px;">
		<table cellpadding="5" cellspacing="5">
		    <tr>
			<td>Browser</td>
			<td>
			    <select name="uats_browser">
				<?php
				    for($i = 0; $i < count($browsers); $i++) {
					echo '<option value="'.$browsers[$i]->id.'">'.$browsers[$i]->name.'</option>';
				    }
				?>
				<!--<optgroup label="Webkit">
				    <option value="chrome">Google Chrome</option>-->
				    <!--<option value="chrome-mobile">Google Chrome Mobile</option>
				    <option value="safari">Apple Safari</option>
				    <option value="safari-mobile">Apple Safari Mobile</option>-->
				<!--</optgroup>-->
				<!--<optgroup label="Gecko">
				    <option value="firefox">Firefox</option>
				</optgroup>
				<optgroup label="Internet Explorer">
				    <option value="ie6">Microsoft Internet Explorer 6</option>
				    <option value="ie7">Microsoft Internet Explorer 7</option>
				    <option value="ie8">Microsoft Internet Explorer 8</option>
				    <option value="ie9">Microsoft Internet Explorer 9</option>
				</optgroup>-->
			    </select>
			</td>
		    </tr>
		    <tr>
			<td>Theme</td>
			<td>
			    <select name="uats_theme">
				<?php
				    foreach($themes as $key => $theme ) {
					?>
					    <option value="<?php echo $theme['Name']; ?>"><?php echo $theme['Name']; ?></option>
					<?php
				    }
				?>
			    </select>
			</td>
		    </tr>
		</table>
	    </div>
	</div>
	<input type="submit" name="save" class="button bold" value="Save">
    </form>
    <h2>Debug mode</h2>
    <div class="postbox">
	<div style="margin: 7px;">
	    <form method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=useragent-template">
		<input type="hidden" name="usts_action" value="updatedebug" />
		<p><label>Debug mode: <input type="checkbox" name="uats_debug" <?php echo $debug; ?> /></label></p>
		<p><input type="submit" value="update" class="button bold" /></p>
	    </form>
	</div>
    </div>
    <?php
	if(get_option('usts_debug') == 'true') {
    ?>
    <table class="widefat page fixed" width="100%" cellpadding="3" cellspacing="3">
	<thead>
	    <tr>
		<th class="manage-column" scope="col">UserAgent</th>
	    </tr>
	</thead>
	<tbody>
	    <?php
		for($i = 0; $i < count($useragents); $i++) {
		    echo '<tr><td>'.$useragents[$i]->useragent.'</td></tr>';
		}
	    ?>
	</tbody>
    </table>
    <form method="post" action="<?php echo bloginfo('wpurl'); ?>/wp-admin/admin.php?page=useragent-template">
	<input type="hidden" name="usts_action" value="truncateua" />
	<input type="submit" name="save" class="button bold" value="Vaciar">
    </form>
    <?php
	}
    ?>
</div>
<br/>
<br/>
<br/>