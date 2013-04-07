<script type="text/javascript" src="http://api.recaptcha.net/js/recaptcha_ajax.js"></script>

<style>
{literal}
.recaptcha_only_if_no_incorrect_sol {
    width: auto !important;
}

#recaptcha_area {
    margin: auto;
}

#errorcaptcha {
    text-align: center;
    height: 40px;
    line-height: 40px;
}
{/literal}
</style>

<script type="text/javascript">
$(window).load(function(){ldelim}
    {if $captcha_erreur == 1}
        $("#errorcaptcha").insertBefore(".std");
    {/if}

    $("#captchacontent").insertAfter(".textarea");
    var RecaptchaOptions = {ldelim}lang: '{$lang_iso}', theme: 'white'{rdelim};
    Recaptcha.create("{$captcha}", 'captchacontent', RecaptchaOptions);
{rdelim});
</script>

<div id="captchacontent">
</div>

{if $captcha_erreur == 1}
<div id="errorcaptcha" class="error">
    {l s="Le CAPTCHA n'est pas correct" mod="captcha"}
</div>
{/if}