<?php

ini_set('log_errors','on');
ini_set('error_log','php/log');
session_start();

//ばいきん格納用
$bacterias =array();

//キッズクラス
class Human{
	protected $name;
	
	protected $hp;
	protected $attackMin;
	protected $attackMax;
	//コンストラクタ
	public function __construct($name, $hp, $attackMin, $attackMax){
		$this->name = $name;
		$this->hp = $hp;
		$this->attackMin =$attackMin;
		$this->attackMax =$attackMax;
	}
	public function setHp($num){
		$this->hp =$num;
	}
	public function getHp(){
		return $this->hp;
	}
	public function attack(){
		$attackPoint = mt_rand($this->attackMin, $this->atackMax);
		if(!mt_rand(0,9)){
			$attackPoint = $attackPonint *1.5;
			$attackPint =(int)$attackPoint;
			History::set_($this->getName().'のつよいこうげき！！');
		}
		$_SESSION['bacteria']->setHp($_SESSION['bacteria']->getHp() - $attackPoint);
		History::set($attackPoint.'ポイントのダメージをあたえた！');
	}
}

//アイテムクラス
class Item {
}

//バクテリアクラス
class Bacteria {
	protected $name;
	protected $hp;
	protected $img;
	protected $attack;
	
	//コンストラクタ
	public function __construct($name, $hp, $img, $attack){
		$this->name = $name;
		$this->hp = $hp;
		$this->img = $img;
		$this->attack = $attack;
	}
	
	//メソッド
	public function attack(){
		$attackPoint = $this->attack;
		if(!mt_rand(0,9)){
			$attackPoint * =1.5;
			$attackPoint =(int)$attackPoint;
			History::set($this->getName().'がおおあばれ！！');
		}
		$_SESSION['human']->setHp($_SESSION['human']->getHp() - $attackPoint);
		History::set($attackPoint.'ポイントのダメージをうけた！');
	}
	public function setHp($num){
		$this->hp = filter_var($num, FILTER_VALIDATE_INT);
	}
	public function setAttack($num){
		$this->attack =(int)filter_var($num, FILTER_VALIDATE_FLOAT);
	}
	public function getName(){
		return $this->name;
	}
	public function getHp(){
		return $this->hp;
	}
	public function getImg(){
		return $this->img;
	}
	public function getAttack(){
		return $this->attack;
	}
}
//履歴管理クラス
class History{
	public static function set($str){
		if(empty($_SESSION['history'])) $_SESSION['history'] ='';
		$_SESSION['history'].=$str.'<br>';
	}
	public static function clear(){
		unset($_SESSION['history']);
	}
}


//インスタンス生成
$human = new Human('ばいきんバスターズ',500,40,120);
$bacterias[] = new Bacteria('ばいスキン',50,'img/mutans-ikkaku_a001.png', mt_rand(20,30));
$bacterias[] = new Bacteria('ばいナンス',80,'img/mutans-giza_b003.png', mt_rand(40,50));
$bacterias[] = new Bacteria('ばいロン',100,'img/mutans-nikaku_a002.png', mt_rand(50,70));
$bacterias[] = new Bacteria('ばいンジャーズ',150,'img/mutans-special003.png', mt_rand(60,100));

function createBacteria(){
	global $bacterias;
	$bacteria = $bacterias[mt_rand(0,3)];
	History::set($bacteria->getName().'があらわれた！');
	$_SESSION['bacteria'] = $bacteria;
}
function init(){
	History::clear();
	History::set('はじめから');
	$_SESSION['knockDownCount'] =0;
	createHuman();
	createBacteria();
}
function gameOver(){
	$_SESSION = array();
}

//post送信後
if(!empty($_POST)){
	$attackFlg = (!empty($_POST['attack'])) ? true : false;
	$startFlg = (!empty($_POST['start'])) _ true :false;
	error_log('POSTされた');
	
	if($startFlg){
		History::set('ゲームをはじめます');
		init();
	} else {
		if($attackFlg){
			//菌に攻撃する
			History::set('こうげきした！');
			$_SESSION['human']->attack();
			//菌から攻撃を食らう
			$_SESSION['bacteria']->attack();
			//HPが０になったら終わり
			if($_SESSION['human']->getHp() <= 0){
				gameOver();
			} else {
				if($_SESSION['bacteria']->getHp() <= 0){
					History::set($_SESSION['bacteria']->getName().'をやっつけた！');
					createBacteria();
					$_SESSION['knockDownCount'] = $_SESSION['knockDownCount'] + 1;
				}
			}
		}
	}
	$_POST =array();
}
?>
<?php
	require('head.php');
?>
	<body>
		<div class="all-wrapper">
			<h1>SAVE THE TEETH</h1>
			<div class="bacteria-wrapper">
				<div class="bacteria-leftwrap">
					<p class="bacteria-name">ばいずきん</p>
					<div class="bacteria-imgwrap">
						<img src="img/mutans-ikkaku_a001.png" alt="" class="bacteria-img">
					</div>
				</div>
				<div class="bacteria-rightwrap">
					<div class="bacteria-msgwrap">
						<p class="bacteria-msg">
							あまいおやつの&thinsp;のこりが<br>
							だ〜いすき！
						</p>
					</div>
					<div class="bacteria-hp">
						<span><i class="fas fa-heart"></i></span>
						<span>HP</span>
						<span>50/50</span>
					</div>
				</div>
			</div>
			<div class="player-wrapper">
				<div class="player-leftwrap">
					<div class="fightLog">たたかいの一部始終が入るエリア</div>
					<div class="player-hp">
						<span><i class="fas fa-heart"></i></span>
						<span>HP</span>
						<span>500/500</span>
					</div>
				</div>
				<div class="player-rightwrap">
					<p class="select-item">▼アイテムをせんたくして、こうげきしよう！</p>
					<div class="items-wrap">
						<div class="item-wrap itemLef">
							<p class="itemName">はぶらし</p>
							<img src="img/hamigakiko_boy.png" alt="">
						</div>
						<div class="item-wrap itemMid">
							<p class="itemName">フロス</p>
							<img src="img/floss_itoyoji_kids_boy.png" alt="">
						</div>
						<div class="item-wrap itemRih">
							<p class="itemName">ゆすぐ</p>
							<img src="img/oral_care_mouthwash.png" alt="">
						</div>
					</div>
					<a href="">
						<p class="reStart">▶︎もういっかい&thinsp;たたかう</p>
					</a>
					<a href="start.php">
						<p class="returnFirst">▶︎いちばんさいしょから</p>
					</a>
				</div>
			</div>
		</div>
		<script src=""></script>
	</body>
</html>