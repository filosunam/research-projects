<p class="list">
  <?php for ($i = 0; $i <= count($rp_funding_programs); $i++) { ?>
    <input name="rp_funding_programs[]" value="<?php if(isset($rp_funding_programs[$i])) echo $rp_funding_programs[$i]; ?>" placeholder="<?php _e('Add program', $this->plugin_slug); ?>" class="widefat" type="text">
  <?php } ?>
</p>

<input type="button" class="button add" value="Add new">
<input type="hidden" name="rp_funding_programs_noncename" id="rp_funding_programs_noncename" value="<?php echo $nonce; ?>">
