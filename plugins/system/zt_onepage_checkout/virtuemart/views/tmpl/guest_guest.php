<?php

/**
 * One Page Checkout For VirtueMart
 *
 * @version     1.0.2
 * @link        http://www.zootemplate.com
 * @author      ZooTemplate
 * @copyright   Copyright (c) 2015 CleverSoft (http://cleversoft.co/)
 * @license     GPL v2
 */

defined('_JEXEC') or die('Restricted access');
$class = 'zt-opc';
?>
<div id="<?php echo $class; ?>-wrap" class="zt-opc-element">
    <!-- Title -->
    <h3 class="<?php echo $class; ?>-title zt-opc-title">
        <?php echo ZtonepageHelperText::_('CHECKOUT_AS_GUEST_OR_REGISTER'); ?>
    </h3>
    <div class="inner-wrap">
        <!-- Guest checkout -->
        <h4 class="<?php echo $class; ?>-subtitle"><?php echo ZtonepageHelperText::_('CHECKOUT_AS_GUEST'); ?></h4>
        <div class="<?php echo $class; ?>-guest-form">
            <div class="<?php echo $class; ?>-inner with-switch">
                <form autocomplete="off" id="<?php echo $class; ?>-user" method="post" data-validation-error="<?php echo ZtonepageHelperText::_('FORM_VALIDATION_ERROR'); ?>">
                    <div class="form-group">
                        <div class="<?php echo $class; ?>-input-group-level">
                            <label for="<?php echo $class; ?>-email" class="email full-input">
                                <span>E-Mail</span>
                            </label>
                        </div>
                        <div class="<?php echo $class; ?>-input <?php echo $class; ?>-input-append">
                            <input type="text" maxlength="100" class="required" value="" size="30" name="email"
                                   id="<?php echo $class; ?>-email" style="width: 279px;">
                        </div>
                    </div>
                    <div class="<?php echo $class; ?>-login-inputs">
                        <div class="form-group">
                            <div class="<?php echo $class; ?>-input <?php echo $class; ?>-input-prepend">
                                <button type="submit" class="btn btn-info"><?php echo ZtonepageHelperText::_('CHECKOUT_AS_GUEST'); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Register form -->
        <h4 class="<?php echo $class; ?>-subtitle"><?php echo ZtonepageHelperText::_('REGISTER'); ?></h4>        
        <div class="<?php echo $class; ?>-reg-form soft-hide">
            <div class="<?php echo $class; ?>-inner with-switch">
                <form autocomplete="off" name="userForm" id="<?php echo $class; ?>-registration" data-validation-error="<?php echo ZtonepageHelperText::_('FORM_VALIDATION_ERROR'); ?>">
                    <div class="form-group">
                        <div class="<?php echo $class; ?>-input-group-level">
                            <label for="<?php echo $class; ?>-email-regis" class="email full-input">
                                <span>E-Mail</span>
                            </label>
                        </div>
                        <div class="<?php echo $class; ?>-input <?php echo $class; ?>-input-append">
                            <input type="text" maxlength="100" class="required" value="" size="30" name="email1" id="<?php echo $class; ?>-email-regis">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="<?php echo $class; ?>-input-group-level">
                            <label for="<?php echo $class; ?>-username" class="username full-input">
                                <span>Username</span>
                            </label>
                        </div>
                        <div class="<?php echo $class; ?>-input <?php echo $class; ?>-input-append">
                            <input type="text" maxlength="25" value="" size="30" name="username" id="<?php echo $class; ?>-username">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="<?php echo $class; ?>-input-group-level">
                            <label for="<?php echo $class; ?>-name" class="name full-input">
                                <span>Displayed Name</span>
                            </label>
                        </div>
                        <div class="<?php echo $class; ?>-input <?php echo $class; ?>-input-append">
                            <input type="text" maxlength="25" value="" size="30" name="name" id="<?php echo $class; ?>-name">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="<?php echo $class; ?>-input-group-level">
                            <label for="<?php echo $class; ?>-password" class="password full-input">
                                <span>Password</span>
                            </label>
                        </div>
                        <div class="<?php echo $class; ?>-input <?php echo $class; ?>-input-append">
                            <input type="password" class="inputbox" size="30" name="password1" id="<?php echo $class; ?>-password">
                            <div class="strength-meter">
                                <div id="meter-status"></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="<?php echo $class; ?>-input-group-level">
                            <label for="<?php echo $class; ?>-password2" class="password2 full-input">
                                <span>Confirm Password</span>
                            </label>
                        </div>
                        <div class="<?php echo $class; ?>-input <?php echo $class; ?>-input-append">
                            <input type="password" class="inputbox" size="30" name="password2" id="<?php echo $class; ?>-password2">
                        </div>
                    </div>

                    <div class="<?php echo $class; ?>-login-inputs">
                        <div class="form-group">
                            <div class="<?php echo $class; ?>-input <?php echo $class; ?>-input-prepend">
                                <button type="submit" class="btn btn-info">Register And Checkout </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="<?php echo $class; ?>-reg-advantages">
           <?php echo ZtonepageHelperText::_('DESCRIPTION'); ?>
        </div>
    </div>
</div>
