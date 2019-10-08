<?php

ini_set('log_errors','on');
ini_set('error_log','php.log');
session_start();


//ばいきん格納用
$bacterias = array();

//キッズクラス
class Human{
	protected $name;
	protected $hp;
	protected $attackMin;
	protected $attackMax;
	public function __construct($name, $hp, $attackMin, $attackMax) {
		$this->name = $name;
		$this->hp = $hp;
		$this->attackMin = $attackMin;
		$this->attackMax = $attackMax;
	}
	public function setName($str){
		$this->name = $str;
	}
	public function getName(){
		return $this->name;
		echo $_SESSION['human']->getName();
	}
	public function setHp($num){
		$this->hp = $num;
	}
	public function getHp(){
		return $this->hp;
	}
	public function attack(){
		$attackPoint = mt_rand($this->attackMin, $this->attackMax);
		if(!mt_rand(0,9)){
			$attackPoint *= 1.5;
			$attackPoint = (int)$attackPoint;
			History::set($this->getName().' のつよいこうげき！！');
		}
		$_SESSION['bacteria']->setHp($_SESSION['bacteria']->getHp() - $attackPoint);
		History::set($attackPoint.'ポイント のダメージをあたえた！');
	}
}


//バクテリアクラス
class Bacteria{
	// プロパティ
	protected $name;
	protected $msg;
	protected $hp;
	protected $img;
	protected $attack;
	// コンストラクタ
	public function __construct($name, $msg, $hp, $img, $attack) {
		$this->name = $name;
		$this->msg = $msg;
		$this->hp = $hp;
		$this->img = $img;
		$this->attack = $attack;
	}
	
	//メソッド
	public function attack(){
		$attackPoint = $this->attack;
		if(!mt_rand(0,9)){
			$attackPoint *= 1.5;
			$attackPoint = (int)$attackPoint;
			History::set($this->getName().' が、おおあばれ!!');
		}
		$_SESSION['human']->setHp( $_SESSION['human']->getHp() - $attackPoint );
		History::set($attackPoint.'ポイント のダメージをうけた！');
	}
	public function setHp($num){
		$this->hp = filter_var($num, FILTER_VALIDATE_INT);
	}
	public function setAttack($num){
		$this->attack = (int)filter_var($num, FILTER_VALIDATE_FLOAT);
	}
	public function getName(){
		return $this->name;
	}
	public function getMsg(){
		return $this->msg;
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
		if(empty($_SESSION['history'])) $_SESSION['history'] = '';
		$_SESSION['history'] .= $str.'<br>';
	}
	public static function clear(){
		unset($_SESSION['history']);
	}
}


//インスタンス生成
$human = new Human('ばいきんバスターズ',500,10,60);
$bacterias[] = new Bacteria('ばいスキン', 'あまいおやつののこりが だ〜いすき！', 50, 'img/mutans-ikkaku_a001.png', mt_rand(20, 30) );
$bacterias[] = new Bacteria('ばいナンス','たくさんたべて はやくねたいよ〜！',80,'img/mutans-giza_b003.png', mt_rand(30,40));
$bacterias[] = new Bacteria('ばいロン','かならず むしばにしてやるぞ〜！',100,'img/mutans-nikaku_a002.png', mt_rand(40,50));
$bacterias[] = new Bacteria('ばいンジャーズ','おれたちのパワーを なめるなよ〜！',150,'img/mutans-special003.png', mt_rand(50,60));

function createBacteria(){
	global $bacterias;
	$bacteria =  $bacterias[mt_rand(0, 3)];
	History::set($bacteria->getName().' があらわれた！');
	$_SESSION['bacteria'] =  $bacteria;
}

function createHuman(){
	global $human;
	$_SESSION['human'] =  $human;
}

function init(){
	History::clear();
	History::set('----START----');
	$_SESSION['knockDownCount'] = 0;
	createHuman();
	createBacteria();
}

function gameOver(){
	$_SESSION = array();
	header('Location:gameOver.php');
}


//post送信後
if(!empty($_POST)){
	$attackFlg = (!empty($_POST['attack'])) ? true : false;
	$startFlg = (!empty($_POST['start'])) ? true : false;
	error_log('POST送信された！');
	error_log($attackFlg);
	error_log($startFlg );
	
	if($startFlg){
		History::set('ゲームをはじめます！');
		init();
	}else{
		if($attackFlg){
			History::set('>>>>>>こうげき！');
			$_SESSION['human']->attack();
			$_SESSION['bacteria']->attack();
			if($_SESSION['human']->getHp() <= 0){
				gameOver();
			}else{
				if($_SESSION['bacteria']->getHp() <= 0){
					History::set($_SESSION['bacteria']->getName().' をやっつけた！');
					createBacteria();
					$_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
				}
			}
		}else{ //ゆすぐを押した場合
			History::set('>>>>>>にげた！');
			createBacteria();
		}
	}
	$_POST = array();
}
?>

<?php
	require('head.php');
?>
	<body>
		<div class="all-wrapper">
			<h1>SAVE THE TEETH</h1>
				<?php if(empty($_SESSION)){ ?>
					<p class="start-explain">
						くちのなかのばいきんと &thinsp; たたかうゲームです<br>
						きみは &thinsp; いくつ&thinsp; ばいきんをたおせるかな？
					</p>
					<div class="start-btn">
						<form method="post" class="start-game">
							<img src="img/character_kanban.png" alt="" class="start-img">
							<input type="submit" name="start" value="▶︎ゲームをはじめる">
						</form>
					</div>
					<p class="returnTop">
						<a href="select.php">&gt;さいしょにもどる</a>
					</p>
				<?php }else{ ?>
					<div class="bacteria-wrapper">
						<div class="bacteria-leftwrap">
							<p class="bacteria-name"><?php echo $_SESSION['bacteria']->getName(); ?></p>
							<div class="bacteria-imgwrap">
								<img src="<?php echo $_SESSION['bacteria']->getImg(); ?>" alt="" class="bacteria-img">
							</div>
						</div>
						<div class="bacteria-rightwrap">
							<div class="bacteria-msgwrap">
								<p class="bacteria-msg">
									<?php echo $_SESSION['bacteria']->getMsg(); ?>
								</p>
							</div>
							<div class="bacteria-hp">
								<span><i class="fas fa-heart"></i></span>
								<span>HP</span>
								<span><?php echo $_SESSION['bacteria']->getHp(); ?></span>
							</div>
						</div>
					</div>
					<div class="player-wrapper">
						<div class="player-leftwrap">
							<div class="fightLog js-scroll">
								<?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?>
							</div>
							<div class="bacteria-count">
								<span><?php echo $_SESSION['knockDownCount']; ?></span>
								<span>たい</span>
								<span>/</span>
							</div>
							<div class="player-hp">
								<span><i class="fas fa-heart"></i></span>
								<span>HP</span>
								<span><?php echo $_SESSION['human']->getHp(); ?></span>
							</div> 
						</div>
						<div class="player-rightwrap">
							<p class="select-item">▼アイテムをせんたくして、こうげきしよう！</p>
							<form method="post">
								<div class="items-wrap">
									<input type="submit" name="attack" value=" " class="item-wrap itemLef">
									<input type="submit" name="attack" value=" " class="item-wrap itemMid">
									<input type="submit" name="escape" value=" " class="item-wrap itemRih">
								</div>
							</form>
							<form method="post">
								<input type="submit" name="start" value="▶もういっかい&thinsp;たたかう" class="reStart">
								<a href="sessionOut.php">
									<p class="returnFirst">▶︎いちばんさいしょから</p>
								</a>
							</form>
						</div>
					</div>
				<?php } ?>
		</div>
		<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
		<script src="script.js"></script>
	</body>
</html>