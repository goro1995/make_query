<?php
	#############################################################################################
	##                          PLIK: func.make_query.php@make_query()                         ##
	##                                   Autor: Maciej Szelag                                  ## 
	##                    Wykorzystanie: Tworzy zapytania do bazy dynamicznie                  ##
	##                                         Uzycie:                                         ##
	##1).Po podlaczeniu modulu (requie('func.make_query.php');)                                ##
	##2).Nalezy uruchomic funkcje w odpowiednim momecie                                        ##
	##-przyklad:                                                                               ##
	##//logowanie                                                                              ##
	##$array1('*');                                                                            ## 
	##$array2('username' => $_POST['username'],'password' => md5($_POST['password']));         ##
	##if(mysql_num_rows(make_query(true,'select','accounts',$array1,$array2))==1){             ##
	##	$login=true;																	       ##
	##}else{																			       ##
	##	$login=false;																	       ##
	##}																					       ##
	##																					       ##
	##-----------------------------------------------------------------------------------------##
	##Jesli podanne dane logowania w $array2 sa poprawne i skrypt znajdzie jeden               ##
	## rekord o z owa kombinacja ustawi zmienna $login na prawde (zaloguje uzytkownika)        ##
	##-----------------------------------------------------------------------------------------##
	##Parametry:                                                                               ##
	## - $send 	 =parametr typu bool przyjmuje wartosc true/false odnosi sie do wyslania       ##
	##            zapytania do bazy i zworcenia tego co baza odpowie(przy true)                ##
	##            zwroceniu samego gotowego zapytania (przy false)                             ##
	## - $type 	 =paramert zawierajacy nazwe funkcji do wykonania dla INSERT INTO jest to      ##
	##            insert dla SELECT jest to select dla UPDATE jest top update i dla DELET      ##
	##            jest do delet                                                                ##
	## - $table  =parametr zawierajacy nazwe tabeli w ktorej wykonywana jest dana czynnosc     ##
	## - $array1 =tablica danych wykorzystywana przy kazdym z pol                              ##
	##            powinna byc zbudowana nastepujaco 'nazwa w mysql' = 'wartosc pola' dla       ##
	##            zapytan typu insert badz update lub jednowymiarowa z nazwem pola mysql       ##
	##            dla zapytan typu select w przypadku delete stoswoana jest tablica typu       ##
	##            'nazwa w mysql' = 'wartosc pola' jako warunek (WHERE)                        ##
	## - $array2 =tablica danych wykorzystywana przy zapytaniach typu select oraz update       ##
	##            gdzie jest uznawana jako warunek (WHERE) i powinna byc zbudowana             ##
	##            'nazwa w mysql' = 'wartosc pola'                                             ##
	##-----------------------------------------------------------------------------------------##
	##Budowa tablic:                                                                           ##
	## -dwu wymiarowe:                                                                         ##
	##  - dodawanie rekordu do bazy danych:                                                    ##
	##    $array=array('nazwa pola w mysql' => 'wartosc pola','kolejna nazwa'=>'wartosc');     ##
	##    przyklad rejestracji:                                                                ##
	##    $array=array('username' => $_POST['username'],'password' => md5($_POST['password']); ##
	## -jedno wymiarowe:                                                                       ##
	##  - argument SELECT:                                                                     ##
	##    $array=array('username','password','email',name');                                   ##
	##-----------------------------------------------------------------------------------------##
	##Przyklad usuwania konta uzytkownika ktory jest wlasnie zalogowany poprzez sesje          ##
	## - $array=array('username' = $_SESSION['username']);                                     ##
	## - $query = make_query(true,delet,'accounts',$array);                                    ##
	##Wyjasnienie w pierwszej lini kodu tj. $array... okreslamy parametr warunku tj. WHERE     ##
	##nadajemu wiec warunek username = $_SESSION['username'].                                  ##
	##w drugiej lini tj. $query = .... podajemy konfiguracje czyli:                            ##
	##true = odwoluje sie do wykonania zapytania w locie zamiast wyswietlania go userowi       ##
	##delet = usatala tryb zapytania w tym przypadku DELETE                                    ##
	##accounts = odwoluje sie do nazwy tabeli w bazie danych mysql w tym wypadku accounts      ##
	##$array = tablica z danymi do warunku czyli WHERE                                         ##
	##                                                                                         ##
	##Wiec tak wpisana funkcja zadziala tak samo jak bysmy wpisali w kod php:                  ##
	##mysql_query("DELETE FROM `accounts` WHRERE `username` '".$_SESSION['username']."'");     ##
	##																						   ##
	##Przyklad pobierania nazwy uzytkownika i jego adresu e-mail ktory jest wlascicielem id 2  ##
	## - $what=array('username','email');                                                      ##
	## - $where=array('id'=>'5');															   ##
	## - $query=make_query(true,'select','accounts',$what,$where);                             ##
	## - while($resul = mysql_fetch_array($query)){											   ##
	## -    $username=$resul['username'];                                                      ##
	## -    $email   =$resul['email'];                                                         ##
	## - }                                                                                     ##
	##Krotki opis powyzszego kodu: $what to tablica jedno wymiarowa zawierajaca nazwy pol do   ##
	##pobrania, $where jest tablica dwu wymiarowa zawierajaca warunek wybierania rekordu       ##
	##$query zmienna do ktorej jest przypiswany rezultat wykonania zapytania                   ##
	##while($resul = mysql_fetch_array($query)){ przypisuje wartosci do zmiennej tymczasowej   ##
	##$resul . Nastepnie wszystko jest zapisywane do normalnych zmiennych ktore mozna uzyc w   ##
	##dalszej czesci skryptu                                                                   ##
	##-----------------------------------------------------------------------------------------##
	##Podsumowujac funkcja make_query bardzo przyspiesza tworzenie zapytan do bazy danych      ##
	##dzieki uzyciu funkcji w skryptach czesto operujacych na bazach danych jestesmy w stanie  ##
	##napisac 20% kodu wiecej w duzo krotszym czasie                                           ##
	#############################################################################################
	function make_query($send=false,$type,$table,$array1,$array2=array()){
		if($type=="insert"){
			$no=0;
			$where=array();
			$what=array();
			foreach($array1 as $key => $value){
				$where[$no]="`".$key."`";
				$what[$no]="'".$value."'";
				$no++;
			}
			$where_ok=implode(',',$where);
			$what_ok=implode(',',$what);
			$sql="INSERT INTO `".$table."` (".$where_ok.")VALUES(".$what_ok.")";
		}elseif($type=="select"){
			$what='';
			foreach($array1 as $value){
				$what.=', `'.$value.'`';
			}
			$what = substr($what,1);
			$where='';
			foreach($array2 as $key => $value){
				$where.="AND `".$key."` = '".$value."'";
			}
			$where = substr($where,3);
			$sql="SELECT ".$what." FROM `".$table."` WHERE ".$where;
		}elseif($type=="update"){
			$what='';
			foreach($array1 as $key => $value){
				$what.=", `".$key."` = '".$value."'";
			}
			$what = substr($what,1);
			$where='';
			foreach($array2 as $key => $value){
				$where.="AND `".$key."` = '".$value."'";
			}
			$where = substr($where,3);
			$sql="UPDATE `".$table."` SET ".$what." WHERE ".$where;
		}elseif($type=="delete"){
			$where='';
			foreach($array1 as $key => $value){
				$where.="AND `".$key."` = '".$value."'";
			}
			$where = substr($where,3);
			$sql="DELETE FROM `".$table."` WHERE ".$where;
		}else{
		
		}
		if($send){
			return mysql_query($sql)or die(mysql_error());
		}else{
			return $sql;
		}
	}
?>
