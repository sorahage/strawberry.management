<?php
// --------------------------------------------------------| 科目読込 |----------
$sql  = 'SELECT no,name,bunrui FROM kamoku WHERE 1 ORDER BY no ASC';
$prepare =  $dbh -> prepare($sql);
$prepare -> execute();
while ( $row = $prepare -> fetch() ) {
	$kamoku[$row['no']]=$row['name'  ];
	$bunrui[$row['no']]=$row['bunrui'];
}

// --------------------------------------------------------| 部門読込 |----------
$sql  = 'SELECT no,name FROM bumon WHERE 1 ORDER BY no ASC';
$prepare =  $dbh -> prepare($sql);
$prepare -> execute();
while ( $row = $prepare -> fetch() ) {
	$bumon[$row['no']]=$row['name'  ];
}
end($bumon);
$bumon_end_key=key($bumon);
reset($bumon);

// --------------------------------------------------------| 担当読込 |----------
$sql  = 'SELECT no,name FROM tanto WHERE 1 ORDER BY no ASC';
$prepare =  $dbh -> prepare($sql);
$prepare -> execute();
while ( $row = $prepare -> fetch() ) {
	$tanto[$row['no']]=$row['name'  ];
}
?>
