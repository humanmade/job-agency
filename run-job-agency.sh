#!/bin/bash          

RUN_JOBS="wp job-agency find-work"

# check how how many jobs we need to do
JOBS=`wp job-agency check-employment-needs`

# check how many workers are working
RUNNING=$(((`ps aux | grep 'job-agency find-work' | wc -l`)-1))
if [ -z "$1" ]; then
	TOTAL_WORKERS=1
else
	TOTAL_WORKERS=$1
fi

TO_HIRE=$(($TOTAL_WORKERS-$RUNNING))

echo "Jobs to do: "$JOBS
echo "Workers: "$RUNNING
echo "Workers to hire: "$TO_HIRE

if [ $JOBS -gt '0' -a $TO_HIRE -gt 0 ]
	then 
		for i in {1..$TO_HIRE} ;
		do 
    		eval ${RUN_JOBS} &
    	done
    ##then echo $JOBS
fi