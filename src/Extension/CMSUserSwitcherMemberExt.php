<?php

namespace SanderVanScheepen\SilverstripeCMSUserSwitcher\Extension;

use SilverStripe\Admin\CMSProfileController;
use SilverStripe\Control\Controller;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Security\Member;
use SilverStripe\Security\Permission;
use SilverStripe\Security\Security;
use SilverStripe\View\Requirements;
use function _t;
use function intval;

/**
 * Class CMSUserSwitcherMemberExt
 *
 * @property bool                            $CMSUserSwitchCanSwitch
 * @property bool                            $CMSUserSwitchCanBeImpersonatedByAdmin
 * @property Member|CMSUserSwitcherMemberExt $owner
 */
class CMSUserSwitcherMemberExt extends DataExtension
{
    private static $db = [
        'CMSUserSwitchCanSwitch'                => 'Boolean',
        'CMSUserSwitchCanBeImpersonatedByAdmin' => 'Boolean',
    ];

    private static $has_one = [];

    private static $has_many = [];

    private static $many_many = [
    ];

    private static $belongs_many_many = [

    ];

    private static $defaults = [];

    public function updateCMSFields(FieldList $oFields)
    {
        /** @var Member $oCurrentMember */
        $oCurrentMember   = Security::getCurrentUser();
        $iCurrentMemberID = intval($oCurrentMember->ID);

        $oFields->removeByName('CMSUserSwitchCanSwitch');
        $oFields->removeByName('CMSUserSwitchCanBeImpersonatedByAdmin');

        if (Permission::check('ADMIN') && ! Controller::curr() instanceof CMSProfileController) {
            $oFields->addFieldToTab('Root.Main', HeaderField::create('HdrCMSUserSwitcherMain', _t('SanderVanScheepen\\SilverstripeCMSUserSwitcher\\Extension\\CMSUserSwitcherMemberExt.HEADER_MAIN', 'Switching identities'), 2));
            $oFields->addFieldToTab('Root.Main', LiteralField::create('LitCMSUserSwitcherIntroduction', '<p>' . _t('SanderVanScheepen\\SilverstripeCMSUserSwitcher\\Extension\\CMSUserSwitcherMemberExt.INTRODUCTION', 'This feature allows administrators after logging in to the CMS to assume the identity of another user.') . '</p>'));

            if (Permission::check('ADMIN', 'any', $this->owner)) {
                $oFields->addFieldToTab('Root.Main', CheckboxField::create('CMSUserSwitchCanSwitch', _t('SanderVanScheepen\\SilverstripeCMSUserSwitcher\\Extension\\CMSUserSwitcherMemberExt.OPTION_ENABLE_FOR_ADMIN', 'Enable CMS user switcher for this account.')));
            }

            $oFields->addFieldToTab('Root.Main', CheckboxField::create('CMSUserSwitchCanBeImpersonatedByAdmin', _t('SanderVanScheepen\\SilverstripeCMSUserSwitcher\\Extension\\CMSUserSwitcherMemberExt.OPTION_ADD_MEMBER_TO_USERSWITCHER', 'Show in CMS user switcher for admins')));
        }
    }

}
