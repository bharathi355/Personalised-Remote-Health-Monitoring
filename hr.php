<?php
//    include "db.php";
	include_once "eliminate_outliers_hr.php";
	function update_hr($pid)
	{
		$host='localhost';
		$uname='root';
		$pwd='';
		$db="project";
		$con = mysql_connect($host,$uname,$pwd) or die("connection failed");
		mysql_select_db($db,$con) or die("db selection failed");
		$agensex=mysql_query("SELECT distinct age,sex FROM patprofile WHERE pid='$pid'") or die(mysql_error());
		$age=0;
		$sex=0;
		$cur_class="normal";
		while($result5=mysql_fetch_assoc($agensex))
		{
			$age=$result5['age'];
			$sex=$result5['sex'];
		}
		$hr_1=array(0,0,0,0,0);
		$centers=mysql_query("SELECT hr_vlow,hr_low,hr_normal,hr_high,hr_vhigh FROM centers WHERE pid='$pid'")or die(mysql_error());
		
		while($result6=mysql_fetch_array($centers))
		{
			$hr_1[0]=$result6['hr_vlow'];
			$hr_1[1]=$result6['hr_low'];
			$hr_1[2]=$result6['hr_normal'];
			$hr_1[3]=$result6['hr_high'];
			$hr_1[4]=$result6['hr_vhigh'];
		}
		$values=mysql_query("SELECT hr FROM summary WHERE pid='$pid' ORDER BY time DESC")or die(mysql_error());
		$count=array(0,0,0,0,0);
		$min=0;
		$clust="";
		$newcenters=array(0,0,0,0,0);
		$values1=array(0,0,0,0,0);
		while($result7=mysql_fetch_assoc($values))
		{
			//echo "<br>Value of hr ".$result7['hr']."<br>";
			$mindis=100;
			for($i=0;$i<5;$i++)
			{
				$x=pow((intval($hr_1[$i])-intval($result7['hr'])),2);
				$dis=sqrt($x); //calculate eucledian distance
				//echo $dis." Distance<br>";
				if($dis<$mindis)
				{
					$min=$i;
					$mindis=$dis;
				}
			}
			$count[$min]++;
			$values1[$min]+=$result7['hr'];
			if($min==0)
				$clust="very low";
			else if($min==1)
				$clust="low";
			else if($min==2)
				$clust="normal";
			else if($min==3)
				$clust="high";
			else if($min==4)
				$clust="very high";
			//break;
		}
		//echo "Heartrate new centers<br>";
		for($i=0;$i<5;$i++)
		{
			//echo "Values ".$i." ".$values1[$i]."<br>";
			//echo "Count  ".$i." ".$count[$i]."<br>";
		}
		for($i=0;$i<5;$i++)
		{
			if($count[$i]!=0)
				$newcenters[$i]=$values1[$i]/$count[$i];
			else
				$newcenters[$i]=$hr_1[$i];
			//echo $newcenters[$i]." ";
		}
		$pers=mysql_query("SELECT * FROM summary WHERE pid='$pid' ORDER BY time DESC") or die(mysql_error());
		$twenty=0;
		$same=0;
		$hrarray=array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);
		while($res=mysql_fetch_array($pers))
		{
			if($twenty==20)
				break;
			$hrarray[$twenty]=$res['hr'];
			$twenty++;
		}
		$majindex=0;
		$maxcnt=1;
		for($i=1;$i<20;$i++)
		{
			if($hrarray[$i]==$hrarray[$majindex])
				$maxcnt++;
			else
				$maxcnt--;
			if($maxcnt==0)
				$majindex=$i;
		}
		$same=$hrarray[$majindex];
		//echo "<br>Current HR class is ".$clust;
		$hr_to_replace=$newcenters[$min];
		//$update_query=mysql_query("UPDATE centers SET cur_class='$clust' WHERE pid='$pid'");	
		$update_query=mysql_query("UPDATE centers SET hr_vlow='$newcenters[0]' WHERE pid='$pid'")or die(mysql_error());
		if(!$update_query)
			//echo "Error 1<br>";
		$update_query=mysql_query("UPDATE centers SET hr_low='$newcenters[1]' WHERE pid='$pid'")or die(mysql_error());
		if(!$update_query)
			//echo "Error 1<br>";
		$update_query=mysql_query("UPDATE centers SET hr_normal='$newcenters[2]' WHERE pid='$pid'")or die(mysql_error());
		if(!$update_query)
			//echo "Error 1<br>";
		$update_query=mysql_query("UPDATE centers SET hr_high='$newcenters[3]' WHERE pid='$pid'")or die(mysql_error());
		if(!$update_query)
			//echo "Error 1<br>";
		$update_query=mysql_query("UPDATE centers SET hr_vhigh='$newcenters[4]' WHERE pid='$pid'")or die(mysql_error());
		if(!$update_query)
			//echo "Error 1<br>";
		//function for replacement of training data
			replace_outliers_hr($pid,$age,$sex,$clust,$hr_to_replace);
	}
	?>