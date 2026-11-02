echo Enter CSV file path:
read csvfile
echo Enter DB name:
read db
echo Enter Table name:
read table
echo Reading CSV file $csvfile ...
#csvfile=$1
#echo $csvfile

colnum=`head -1 $csvfile | awk  '{ num=split($0,a,","); for(i=1;i<=num;i++) { where = match(a[i],"phone"); if(where!=0) { fieldno = i ; print fieldno; } } }'` 


#cut -f $colnum -d "," $csvfile | awk  '{ num=split($0,a,"\n"); for(i=1;i<=num;i++) { print a[i]; }  }' 

phone=`cut -f $colnum -d "," $csvfile | sed '1d'`  
for i in $phone 
do
rm -f ./a.sql
echo "delete from $table where phone=$i"";">a.sql
echo Processing: $i
psql -d $db -U postgres -f a.sql
done
echo DONE.
echo HAVE A GUD DAY !!!
