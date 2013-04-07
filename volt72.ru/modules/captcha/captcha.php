<?php

class Captcha extends Module
{
    private $public_key = '6LfkxgoAAAAAAIFeil29njnaks1wv9isHjS1VO5o';
    private $private_key = '6LfkxgoAAAAAAAN3O9_fBtON1lKm9tf5sDzO-t-e';
    
    function __construct()
    {
        $this->name = 'captcha';
        $this->tab = 'PierreYves.be';
        $this->version = '1.2';

        parent::__construct();

        $this->displayName = $this->l('Captcha');
        $this->description = $this->l('Add a captcha in contact page');
    }
    
    public function install()
    {
        if (!parent::install())
            return false;
            
        if (!$this->registerHook('header'))
            return false;
            
        return true;
    }
    
    public function uninstall()
    {
        if (!parent::uninstall())
            return false;
            
        return true;
    }
    
    public function hookHeader($params)
    {
        global $page_name, $smarty;
        
        //if ($page_name != 'contact-form')
        //    return '';
        
        require_once(_PS_MODULE_DIR_.$this->name.'/lib/recaptchalib.php');
        
        if (Tools::isSubmit('submitMessage'))
        {
            $resp = recaptcha_check_answer($this->private_key, $_SERVER["REMOTE_ADDR"], $_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);

            if (!$resp->is_valid)
            {
                Tools::redirect('contact-form.php?captcha_erreur=1');
            }
        }
        else
        {
            if (isset($_GET['captcha_erreur']) && $_GET['captcha_erreur'] == 1)
                $smarty->assign('captcha_erreur', 1);
            else 
                $smarty->assign('captcha_erreur', 0);
                
            $error = null;
            $smarty->assign('captcha', $this->public_key);
            return $this->display(__FILE__, 'captcha.tpl');
        }
    }
}