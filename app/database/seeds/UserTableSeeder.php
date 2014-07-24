<?php

class UserTableSeeder extends Seeder{

	public function run(){
		
		#DB::table('users')->truncate();

		User::create(array(
            'group_id'=>1,
			'name'=>'Администратор',
			'surname'=>'',
			'email'=>'admin@infiniti-gedon.ru',
			'active'=>1,
			'password'=>Hash::make('grapheme1234'),
			'photo'=>'',
			'thumbnail'=>'',
			'temporary_code'=>'',
			'code_life'=>0,
		));

		User::create(array(
            'group_id'=>2,
			'name'=>'Пользователь',
			'surname'=>'',
			'email'=>'user@infiniti-gedon.ru',
			'active'=>1,
			'password'=>Hash::make('000000000'),
			'photo'=>'',
			'thumbnail'=>'',
			'temporary_code'=>'',
			'code_life'=>0,
		));

		User::create(array(
            'group_id'=>3,
			'name'=>'Модератор',
			'surname'=>'',
			'email'=>'moder@infiniti-gedon.ru',
			'active'=>1,
			'password'=>Hash::make('111111111'),
			'photo'=>'',
			'thumbnail'=>'',
			'temporary_code'=>'',
			'code_life'=>0,
		));
	}

}