<?php

	namespace SanderVanScheepen\SilverstripeCMSUserSwitcher\Extension;

	use SilverStripe\Admin\CMSProfileController;
    use SilverStripe\Forms\CheckboxField;
	use SilverStripe\Forms\FieldList;
	use SilverStripe\ORM\DataExtension;
	use SilverStripe\Security\Member;
    use SilverStripe\Security\Permission;
	use function _t;

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
		    if(Permission::check('ADMIN') && ! Controller::curr() instanceof CMSProfileController) {
                $oFields->push(CheckboxField::create('CanBeImpersonatedByAdmin', _t('SanderVanScheepen\\SilverstripeCMSUserSwitcher\\Extension', 'Show in CMS user switcher for admins')));
            }
		    else {
		        $oFields->removeByName('CanBeImpersonatedByAdmin');
            }
		}

	}
