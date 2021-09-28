UPDATE 
	employee_attandance a
SET
	a.to_date = CONCAT(DATE(a.from_date),' ',(select e.out_time from employee e where e.contact_id = a.employee_id ))
WHERE
	a.to_date is null;




UPDATE 
	employee_attandance a
SET
	late_coming = TIMESTAMPDIFF(MINUTE,CONCAT(DATE(a.from_date),' ',(select in_time from employee e where e.contact_id = a.employee_id )),from_date),
	early_leave  = TIMESTAMPDIFF(MINUTE,to_date,CONCAT(DATE(a.from_date),' ',(select out_time from employee e where e.contact_id = a.employee_id ))),
	total_work_in_mintues = TIMESTAMPDIFF(MINUTE,from_date,to_date),
	total_movement_in = (select count(*) from employee_movement WHERE employee_id = a.employee_id AND DATE(movement_at) = DATE(a.from_date) AND direction="In"),
	total_movement_out = (select count(*) from employee_movement WHERE employee_id = a.employee_id AND DATE(movement_at) = DATE(a.from_date) AND direction="Out");