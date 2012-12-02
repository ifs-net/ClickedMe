<h1>{gt text="See who visited your profile"}</h1>

<p>
    {gt text="These are the last visitors of your user profile page"}
</p>
<ul>
{foreach from=$visits item=visit}
    <li>
        {usergetvar name="uname" uid=$visit.createdUserId assign="uname"}
        {$uname|profilelinkbyuname}
    </li>
{foreachelse}
    <li>{gt text="Your profile has not been visited yet"}</li>
{/foreach}
</ul>

{if $waitingTime > 0 }
    <div class="z-informationmsg">
        {gt text="You had tracking deactivated before. There is a waiting period for you until you can view again who else viewed your profile (The remaining waiting period is %s days)" tag1=$waitingTime}
    </div>
{/if}

<h1>{gt text="Change privacy settings"}</h1>

    {form cssClass="z-form"}
    {formvalidationsummary}

    {insert name="getstatusmsg"}
    <div class="z-informationmsg">
        {gt text="You can disable user tracking for your account to make other users unable to see that you visited their profile. Please do not deactivate tracking to view other profiles and reactivate it afterwards. After reactivating tracking again (because you are interested in those who visited your profile) there will be a waiting period of 7 days until you can see again who visited your profile again."}
    </div>
    
    {formcheckbox id='ClickedMe_trackingDisabled' readOnly=false mandatory=false}
    {formlabel for='ClickedMe_trackingDisabled' mandatorysym='0' __text='Disable tracking for my account'}

    

    <div class="z-formbuttons z-buttons">
    {formbutton class="z-bt-ok" commandName="save" __text="Save"}
    {formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
    
    {/form}
<p>
</p>