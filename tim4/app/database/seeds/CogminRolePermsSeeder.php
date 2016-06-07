<?php

use Cogent\Seeder\AbstractCogminRolePermsSeeder;

final class CogminRolePermsSeeder extends AbstractCogminRolePermsSeeder
{
	/**
	 * Application-defined collection of roles and permissions
	 *
	 * @return array
	 */
	protected function getRolePerms()
	{
		return [
			'tim_admin' =>
			[
				'ldap_group'  => '#TelcoBilling Mgmt',
				'permissions' =>
				[
					'tim_admin_update_invoices',
				]
			],

			'tim_public' =>
			[
				'ldap_group'  => '#TelcoBilling',
				'permissions' =>
				[
					'tim_public_view',
				]
			],
		];
	}
}