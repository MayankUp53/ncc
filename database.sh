#! /bin/bash 


if [ $# = 0 ]; then
  echo "Usage: database.sh <database-name>"
  exit 1
fi
crmdb=$1

# Get path to execution directory from curreny directory
dirpath="${0%database.sh}"


# Create database
echo
echo 2. "Creating database '$crmdb'"
echo

out=`createdb -O postgres -U postgres $crmdb 2>&1`
if [ $? -ne 0 ]; then
	if [ "${out%exists}" = "$out" ] ; then
		echo -e "\tCan not continue.. Exiting..."
		exit 2
	else 
		read -p "	An instance of $crmdb already exists.. would you like to drop it (y/n): " var
		if [ $var = "y" ] || [ $var = "Y" ]; then 
			echo -e "\t\tDropping database $crmdb"
			out=`dropdb -U postgres $crmdb 2>&1`
			if [ $? -eq 0 ]; then 
				echo -e "\t\tDone"
				echo -e "\t\tRecreating database $crmdb"
				out=`createdb -O postgres -U postgres $crmdb 2>&1`
				if [ $? -ne 0 ]; then
					echo -e "\t\tCould not create database. Perform the following\n\t\t\t- Create instance of database $crmdb\n\t\t\t-Populate using ''createtables.sql'query"
					exit 3
				else
					echo -e "\t\tDone"
				fi
			else
				echo -e "\t\tCould not drop the database $crmdb\n\t\t$out\n\t\tExisting..."
				exit 6
			fi
		else
			echo -e "\tDatabase creation aborted on user request\n\nExiting..."
			exit 4
		fi
	fi
	echo -e "\tDone"
else 
	echo -e "\tDone"
fi

# Create plpgsql language for database
echo
echo 3. Creating database $crmdb
echo
out=`createlang -d $crmdb -U postgres plpgsql 2>&1`
if [ $? -ne 0 ]; then
	echo -e "\tCould not create lang 'plpgsql'\n\tContinuing..."
else 
	echo -e "\tDone"
fi

# Run the table initialization sql
echo
echo 4. Initializing database $crmdb
echo
out=`psql -d $crmdb -U postgres -f CrmDB_Tables.sql 2>&1`
if [ $? -ne 0 ]; then
	echo -e "--------------------------------------------------------------------------------"
	echo $out
	echo -e "--------------------------------------------------------------------------------"
	echo -e "\tError while initializing the tables in database"
	exit 5 
else 
	echo -e "\tDone"
fi

