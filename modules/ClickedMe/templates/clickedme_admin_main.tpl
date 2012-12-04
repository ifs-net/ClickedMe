{adminheader}

<div class="z-admin-content-pagetitle">
    {icon type="config" size="small"}
    <h3>{gt text="Administration for Module ClickedMe"}</h3>
</div>

{form cssClass="z-form"}
{formvalidationsummary}

<p>
{formlabel for='ClickedMe_waitingPeriod' __text='Waiting period (in days) a user cannot see who visited the own profile after tracking for the own account was deactivated' mandatorysym='1'}
{formintinput minValue="0" maxValue="100" id='ClickedMe_waitingPeriod' mandatory=true __title='Waiting period in days' cssClass='required validate-digits'}
</p>

<div class="z-formbuttons z-buttons">
{formbutton class="z-bt-ok" commandName="save" __text="Save"}
{formbutton class="z-bt-cancel" commandName="cancel" __text="Cancel"}
</div>
{/form}

<div class="z-informationmsg">
    <p>
        {gt text="To activate user tracking you have to integrate some code into the template of your profile module."}
    </p>
    <ul>
        <li>MyProfile: <i>Code will follow later!</i></li>
        <li>Profile (modules/Profile/templates/profile_user_view.tpl)</li>
        <li>{gt text="Code to insert into template"}
            <pre>&#123; modapifunc modname='ClickedMe' type='user' func='trackVisit'&#125; </pre>
        </li>
    </ul>
</div>

{adminfooter}
