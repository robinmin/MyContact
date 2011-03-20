BEGIN{
	FS	= ":";
	OFS	= ",";
}

(NR>2){
	strAll = $0;
	gsub(/^.*L10N\(['"]/,"",strAll);
	gsub(/['"]\).*$/,"",strAll);
	print $1,$2,strAll;
#	printf(",%s\t: ''\r\n",strAll);
}
