{gt text="These users have just visited your profile"}:<br />

{if $visits|count gt 0}
    {foreach from=$visits item=visit}
        {usergetvar name="uname" uid=$visit.createdUserId assign="uname"}
        {$uname|profilelinkbyuname}, 
    {foreachelse}
        <li>{gt text="Your profile has not been visited yet"}</li>
    {/foreach}
    {gt text="take a look at their profile, too"}!
{else}
    {gt text="Your profile has not been visited yet"}.
{/if}
