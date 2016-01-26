{include file="header.tpl"}
<div class="site_title">{$lang.txt.account}</div>
<div class="site_content">
<!-- Content -->
{if $smarty.get.page != 'upgrade'}    
<div style="display:table; width:100%">
<div style="display:table-cell; width:210px">
<ul  class="member_sidebar">        
    <li><div class="title">{$lang.txt.global}</div>
    	<ul>
            <li><a href="index.php?view=account&page=summary">{$lang.txt.accsummary}</a></li>
            {if $settings.message_system == 'yes'}
            <li><a href="index.php?view=account&page=messages">{$lang.txt.message_system} ({if $unread_messages==0}0{else}<strong>{$unread_messages}</strong>{/if})</a></li>
            {/if}
            <li><a href="index.php?view=account&page=addfunds">{$lang.txt.addfunds}</a></li>
            <li><a href="index.php?view=account&page=upgrade">{if $user_info.type == 1}{$lang.txt.upgaccount}{else}{$lang.txt.extmembership}{/if}</a></li>
            <li><a href="index.php?view=account&page=withdraw">{$lang.txt.withdraw}</a></li>
            <li><a href="index.php?view=account&page=banners">{$lang.txt.banners}</a></li>
		</ul>
	</li>    
    <li><div class="title">{$lang.txt.settings}</div>
			<ul>
                <li><a href="index.php?view=account&page=settings">{$lang.txt.personal}</a></li>
                {if $settings.forum_active == 'yes'}
                <li><a href="index.php?view=account&page=forum_settings">{$lang.txt.forum_settings}</a></li>
                {/if}
                <li><a href="index.php?view=account&page=manageads">{$lang.txt.advertiser_panel}</a></li>
            </ul>
    </li>
    
    <li><div class="title">{$lang.txt.referrals}</div>
        <ul>
            <li><a href="index.php?view=account&page=referrals">{$lang.txt.directrefs}</a></li>
            {if $settings.rent_referrals == 'yes'}
            <li><a href="index.php?view=account&page=rented_referrals">{$lang.txt.rentedrefs}</a></li>
            <li><a href="index.php?view=account&page=rentreferrals">{$lang.txt.rentrefs}</a></li>
            {/if}
            
            {if $settings.buy_referrals == 'yes'}
            <li><a href="index.php?view=account&page=buyreferrals">{$lang.txt.buyrefs}</a></li>
            <li><a href="index.php?view=account&page=referal_constant">Mis referidos</a></li>
            {/if}
        </ul>
    </li>
    
    <li><div class="title">{$lang.txt.logs}</div>
        <ul>
            {if $settings.ptsu_available == 'yes'}
             <li><a href="index.php?view=account&page=ptsu_history">{$lang.txt.ptsuhistory}</a></li>
            {/if}
            <li><a href="index.php?view=account&page=history">{$lang.txt.orderhistory}</a></li>
            <li><a href="index.php?view=account&page=deposit_history">{$lang.txt.deposithistory}</a></li>
            <li><a href="index.php?view=account&page=withdraw_history">{$lang.txt.withdrawhistory}</a></li>
            <li><a href="index.php?view=account&page=login">{$lang.txt.loginhistory}</a></li>
        </ul>
    </li>
    
    <li><div class="title">{$lang.txt.other}</div>
        <ul>
            <li><a href="index.php?view=account&page=profitcalculator">{$lang.txt.profit_calculator}</a></li>
        </ul>
    </li>        
</ul>    
</div>
<div style="display:table-cell; padding-left:20px">
{/if}        
        	<!-- Content -->
            {include file="$file_name"}
            <!-- End Content -->
{if $smarty.get.page != 'upgrade'}  
</div>
</div>
{/if}
<div class="clear"></div>


</div>
<!-- End Content -->

<!-- End Content -->
{include file="footer.tpl"}