<?php
	
	namespace SanderVanScheepen\SilverstripeCMSUserSwitcher\Extension;
	
	use SanderVanScheepen\SilverstripeCMSUserSwitcher\Controller\Admin\UserSwitcherController;
	use SilverStripe\ORM\ArrayList;
	use SilverStripe\ORM\DataExtension;
	use SilverStripe\Security\Member;
	use SilverStripe\Security\Security;
	use SilverStripe\View\ArrayData;
	use SilverStripe\View\Requirements;
	
	class UserSwitcherDropdownLeftAndMainExt extends DataExtension
	{
		
		public function init()
		{
			Requirements::javascript('sandervanscheepen/silverstripe-cms-userswitcher:client/dist/js/LeftAndMain_UserSwitcher.js');
		}
		
		public function SwitchableMembers()
		{
			$output = ArrayList::create();
			Requirements::javascript('sandervanscheepen/silverstripe-cms-userswitcher:client/dist/js/LeftAndMain_UserSwitcher.js');
			
			$dlMembers = UserSwitcherController::getSwitchableMembers();
			
			/** @var Member $oCurrentMember */
			$oCurrentMember = Security::getCurrentUser();
			
			/** @var Member $oMember */
			foreach ($dlMembers as $oMember) {
				$sCurrentState = intval($oMember->ID) === intval($oCurrentMember->ID) ? 'selected' : '';
				
				$output->push(ArrayData::create([
					'CurrentState' => $sCurrentState,
					'ID'           => $oMember->ID,
					'Title'        => $oMember->getFullName(),
					'MemberID'     => $oMember->ID
				]));
			}
			
			return $output;
		}
	}
