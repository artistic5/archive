<?php

return [
	'1' => [
		/**
		Race 1
		*/
		'favorites' => '1, 3, 5, 11, 12',
		'inter(fav 1, fav 3)' => '1, 4, 5, 7, 10',
		'inter(fav 1, fav 5)' => '1, 5, 7, 8',
		'inter(fav 1, fav 11)' => '4, 7, 8, 11',
		'inter(fav 1, fav 12)' => '10',
		'inter(fav 3, fav 5)' => '1, 5, 7',
		'inter(fav 3, fav 11)' => '4, 7',
		'inter(fav 3, fav 12)' => '10',
		'inter(fav 5, fav 11)' => '7, 8, 12',
		'union' => '1, 4, 5, 7, 8, 10, 11, 12',
	],
	'2' => [
		/**
		Race 2
		*/
		'favorites' => '6, 7, 10, 11',
		'inter(fav 6, fav 7)' => '2, 4',
		'inter(fav 6, fav 11)' => '8',
		'union' => '2, 4, 8',
	],
	'3' => [
		/**
		Race 3
		*/
		'favorites' => '2, 3, 5, 7, 10, 11, 12',
		'inter(fav 2, fav 3)' => '1, 8, 10, 12',
		'inter(fav 2, fav 5)' => '5, 8, 10',
		'inter(fav 2, fav 7)' => '1, 5, 12',
		'inter(fav 2, fav 11)' => '5',
		'inter(fav 2, fav 12)' => '2, 5, 10, 12',
		'inter(fav 3, fav 5)' => '4, 7, 8, 10',
		'inter(fav 3, fav 7)' => '1, 3, 7, 12',
		'inter(fav 3, fav 10)' => '4, 9',
		'inter(fav 3, fav 12)' => '10, 12',
		'inter(fav 5, fav 7)' => '5, 7',
		'inter(fav 5, fav 10)' => '4',
		'inter(fav 5, fav 11)' => '5',
		'inter(fav 5, fav 12)' => '5, 10',
		'inter(fav 7, fav 11)' => '5, 11',
		'inter(fav 7, fav 12)' => '5, 12',
		'inter(fav 11, fav 12)' => '5',
		'union' => '1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 12',
	],
	'4' => [
		/**
		Race 4
		*/
		'favorites' => '1, 2, 4, 10, 12',
		'inter(fav 1, fav 2)' => '2, 3, 5, 10, 11',
		'inter(fav 1, fav 4)' => '3, 11',
		'inter(fav 1, fav 10)' => '10',
		'inter(fav 1, fav 12)' => '3',
		'inter(fav 2, fav 4)' => '3, 9, 11, 12',
		'inter(fav 2, fav 10)' => '10, 12',
		'inter(fav 2, fav 12)' => '3, 12',
		'inter(fav 4, fav 10)' => '4, 12',
		'inter(fav 4, fav 12)' => '3, 12',
		'inter(fav 10, fav 12)' => '12',
		'union' => '2, 3, 4, 5, 9, 10, 11, 12',
	],
	'5' => [
		/**
		Race 5
		*/
		'favorites' => '3, 4, 5, 7, 8',
		'inter(fav 3, fav 4)' => '1, 3, 4, 5, 7',
		'inter(fav 3, fav 5)' => '1, 3, 4, 5',
		'inter(fav 3, fav 7)' => '1, 4, 5, 7',
		'inter(fav 3, fav 8)' => '1, 4, 5, 7',
		'inter(fav 4, fav 5)' => '1, 3, 4, 5',
		'inter(fav 4, fav 7)' => '1, 4, 5, 7',
		'inter(fav 4, fav 8)' => '1, 4, 5, 7',
		'inter(fav 5, fav 7)' => '1, 4, 5',
		'inter(fav 5, fav 8)' => '1, 4, 5',
		'inter(fav 7, fav 8)' => '1, 4, 5, 7, 8',
		'union' => '1, 3, 4, 5, 7, 8',
		'win' => '3, 4, 5, 7, 8',
	],
	'6' => [
		/**
		Race 6
		*/
		'favorites' => '1, 2, 3, 5',
		'inter(fav 1, fav 2)' => '3',
		'inter(fav 1, fav 3)' => '1, 3, 7, 9',
		'inter(fav 1, fav 5)' => '1',
		'inter(fav 2, fav 3)' => '2, 3, 5',
		'inter(fav 3, fav 5)' => '1',
		'union' => '1, 2, 3, 5, 7, 9',
		'win' => '1, 2, 3, 5',
	],
	'7' => [
		/**
		Race 7
		*/
		'favorites' => '5, 8, 10, 12, 13',
		'inter(fav 5, fav 8)' => '2',
		'inter(fav 5, fav 10)' => '10',
		'inter(fav 5, fav 12)' => '5, 10',
		'inter(fav 8, fav 10)' => '8',
		'inter(fav 10, fav 12)' => '10',
		'union' => '2, 5, 8, 10',
	],
	'8' => [
		/**
		Race 8
		*/
		'favorites' => '3, 5, 6, 7, 8',
		'inter(fav 3, fav 5)' => '5, 7, 8',
		'inter(fav 3, fav 6)' => '6, 8, 12',
		'inter(fav 3, fav 7)' => '1, 5',
		'inter(fav 3, fav 8)' => '1, 6, 8, 12',
		'inter(fav 5, fav 6)' => '4, 8, 11',
		'inter(fav 5, fav 7)' => '3, 4, 5',
		'inter(fav 5, fav 8)' => '3, 8',
		'inter(fav 6, fav 7)' => '4',
		'inter(fav 6, fav 8)' => '6, 8, 12',
		'inter(fav 7, fav 8)' => '1, 2, 3',
		'union' => '1, 2, 3, 4, 5, 6, 7, 8, 11, 12',
		'win' => '3, 5, 6, 7, 8',
	],
];
