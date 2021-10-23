select
	t_employee.id,
	t_employee.id_organization,
	t_organization.name as organization,
	t_employee.name,
	t_employee.email,
	t_employee.code,
	t_employee.birthday,
	t_employee.status,
    t_employee.created_at,
    t_employee.created_by
from prefix_mst_employee t_employee
join prefix_mst_organization t_organization on t_organization.id = t_employee.id_organization
/* where condition */