<div style="float:right;width:220px;margin-left:10px;border: 1px solid #ddd;background: #fdffee; padding: 10px 0 10px 10px;">
 	<h2 style="margin: 0 0 5px 0 !important;"><?php _e('More Information', LYF_DOMAIN); ?></h2>
 	<ul id="dbx-content" style="text-decoration:none;">

<?php
if ( "on" == $enableUserFolders && !current_user_can( 'delete_users' ) )
{
?>
    	<li><table border="0">
    		<tr>
    			<td><a href="http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=wanderer" target="_blank"><img src="https://tracking.hostgator.com/img/Shared/120x90.gif" border="0"></a></td>
    			<td><?php printf( __('Want to have your own site? Try %s!', LYF_DOMAIN), '<a style="text-decoration:none;" href="http://secure.hostgator.com/~affiliat/cgi-bin/affiliates/clickthru.cgi?id=wanderer" target="_blank">HostGator</a>'); ?></td>
    		</tr>
    	</table></li>
<?php

}
else
{

?>
    	<li><img src="<?php echo $pluginFolder;?>help.png"><a style="text-decoration:none;" href="http://www.wandererllc.com/company/plugins/listyofiles/" target="_blank"> <?php _e('Support and Help', LYF_DOMAIN); ?></a> </li>
		<li><a style="text-decoration:none;" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TC7MECF2DJHHY&lc=US" target="_blank"><img src="<?php echo $pluginFolder;?>paypal.gif"></a></li>
    	<li><table border="0">
    		<tr>
    			<td><a href="http://member.wishlistproducts.com/wlp.php?af=1080050" target="_blank"><img src="http://www.wishlistproducts.com/affiliatetools/images/WLM_120X60.gif" border="0"></a></td>
    			<td><?php printf( __('Restrict files to registered users? Try %s', LYF_DOMAIN), '<a style="text-decoration:none;" href="http://member.wishlistproducts.com/wlp.php?af=1080050" target="_blank">Wishlist</a>'); ?></td>
    		</tr>
    	</table></li>
    	<li><table border="0">
    		<tr>
    			<td><a href="http://www.woothemes.com/amember/go.php?r=39127&i=b18" target="_blank"><img src="http://woothemes.com/ads/120x90c.jpg" border=0 alt="WooThemes - WordPress themes for everyone" width=120 height=90></a></td>
    			<td><?php printf( __('Make your site <em>stunning</em> with %s', LYF_DOMAIN), '<a style="text-decoration:none;" href="http://www.woothemes.com/amember/go.php?r=39127&i=b18" target="_blank">WooThemes for WordPress</a>'); ?></td>
    		</tr>
    	</table></li>
    	<li><?php printf( __('Contact %s to sponsor a feature or write a plugin just for you.', LYF_DOMAIN), '<a href="http://www.wandererllc.com/company/contact/" target="_blank">Wanderer LLC</a>'); ?></li>
    	<li><?php printf( __('Leave a good rating or comments for %s.', LYF_DOMAIN), '<a href="http://wordpress.org/extend/plugins/list-yo-files/" target="_blank">this plugin</a>' ); ?></li>
<?php
}
?>
	</ul>
</div>
