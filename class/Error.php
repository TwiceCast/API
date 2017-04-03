<?php
class ERR
{
	const SUCCESS		=	0;			//0b00000000;
	const OK			=	1;			//0b00000000;
	const NICKUSED		=	2;			//0b00000001;
	const EMAILUSED		=	4;			//0b00000010;
	const MISSPARAM		=	8;			//0b00000100;
	const DOESNOTEXIST	=	16;			//0b00001000;
	const NORIGHT		=	32;			//0b00010000;
	const UNKNOW		=	64;			//0b00100000;
	const ORGNAMEUSED	=	128;		//0b01000000;
}
?>