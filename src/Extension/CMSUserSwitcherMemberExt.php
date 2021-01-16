<?php

    namespace SanderVanScheepen\SilverstripeCMSUserSwitcher\Extension;

    use SilverStripe\Admin\CMSProfileController;
    use SilverStripe\Control\Controller;
    use SilverStripe\Forms\CheckboxField;
    use SilverStripe\Forms\FieldList;
    use SilverStripe\ORM\DataExtension;
    use SilverStripe\Security\Member;
    use SilverStripe\Security\Permission;
    use SilverStripe\Security\Security;
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

                if (Permission::check('ADMIN', 'any', $this->owner)) {
                    $oFields->push(CheckboxField::create('CMSUserSwitchCanSwitch', _t('SanderVanScheepen\\SilverstripeCMSUserSwitcher\\Extension\\CMSUserSwitcherMemberExt.ENABLE_FOR_ADMIN', 'Enable CMS user switcher for this account.')));
                }

                $oFields->push(CheckboxField::create('CMSUserSwitchCanBeImpersonatedByAdmin', _t('SanderVanScheepen\\SilverstripeCMSUserSwitcher\\Extension\\CMSUserSwitcherMemberExt.ADD_MEMBER_TO_USERSWITCHER', 'Show in CMS user switcher for admins')));
            }
        }

    }
