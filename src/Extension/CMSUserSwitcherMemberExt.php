<?php

	namespace SanderVanScheepen\SilverstripeCMSUserSwitcher\Extension;

	use SilverStripe\Admin\CMSProfileController;
    use SilverStripe\Control\Controller;
    use SilverStripe\Forms\CheckboxField;
	use SilverStripe\Forms\FieldList;
	use SilverStripe\ORM\DataExtension;
    use SilverStripe\Security\Security;
	use SilverStripe\Security\Member;
    use SilverStripe\Security\Permission;
	use function _t;
    use function intval;

    /**
	 * Class CMSUserSwitcherMemberExt
	 *
	 * @property bool $CanBeImpersonatedByAdmin
	 * @property Member|CMSUserSwitcherMemberExt $owner
	 */
	class CMSUserSwitcherMemberExt extends DataExtension
	{
		private static $db = [
			'CanBeImpersonatedByAdmin' => 'Boolean',
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

		    if(Permission::check('ADMIN') && ! Controller::curr() instanceof CMSProfileController) {
		        $sTitle = _t('SanderVanScheepen\\SilverstripeCMSUserSwitcher\\Extension', 'Show in CMS user switcher for admins');

		        if($iCurrentMemberID === intval($this->owner->ID)) {
		            $sTitle = _t('SanderVanScheepen\\SilverstripeCMSUserSwitcher\\Extension', 'Enable CMS user switcher for this account.');
                }

                $oFields->push(CheckboxField::create('CanBeImpersonatedByAdmin', $sTitle));
            }
		    else {
		        $oFields->removeByName('CanBeImpersonatedByAdmin');
            }
		}

	}
