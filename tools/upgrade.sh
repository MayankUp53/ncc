echo Enter DB name:
read db
echo Enter Table name:
read table
echo Enter the Number of fields to be added
read count
future_date=`date --date='2 years' +%Y-%m-%d`
#current_date=`date +%Y%m%d`
#future_date=$((current_date+20000))

while [ $count -ne 0 ]
do 
#rm -f ./b.sql
echo Enter the field to be added:
read field
echo Enter type of field:
read type
sql_cmd=`echo "Alter table $table add column $field $type"";"` 
echo Adding field....$field
#echo "$sql_cmd"
psql -d $db -U postgres -c "$sql_cmd"
if [ "$field" = "date_added" -a  $? -eq 0 ]; then
	sql_cmd=`echo "Update $table set date_added='$future_date'"";"`
	psql -d $db -U postgres -c "$sql_cmd"
fi
count=$((count-1))
done
echo DONE.
echo HAVE A GUD DAY !!!
