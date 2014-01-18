make_query
==========
POLISH
Funkcja make_query jest delikatnym rozszezeniem obslugi baz danych mysql w PHP
funkcja ta pozwala skrocic czas potrzebny na tworzenie nowych skryptow z duzym wykorzystaniem polaczen z bazami danych.
NP. przy standardowej rejestracji trzeba wpisac cos takiego:
$sql="INSERT INTO `users` (`name`,`lastname`,`country`,`ip`,`username`,`password`) VALUES ('".$name."' ,'".$lastname."' ,'".$country."' ,'".$ip"', '".$username."', '".$password."')";
mysql_query($sql);

aby zarejestrowac osobe przy uzyciu make_query mozemy zrobic to wiele prosciej np:
$array=Array('name' =>$name,'lastname'=>$lastname,'country'=>$country,'ip'=>$ip,'username'=>$username,'password'=>$password);
make_query(true,'insert','users',$array);
i to wszystko zapytanie zostanie wyslane do bazy prawda ze prosciej ?

W aktualnej wersji make_query obsluguje podstawowe zapytania typu INSERT INTO , SELECT , UPDATE , DELETE bez dodaktowych parametrow typu np. LIMIT lub ORDER BY zostana one dodane najszybciej jak to bedzie mozliwe
