select
    count(t_structure.id)
from prefix_mst_structure t_structure
join prefix_mst_organization t_organization on t_organization.id = t_structure.id_organization and t_organization.status = 1
join prefix_mst_employee t_employee on t_employee.id = t_structure.id_employee and t_employee.status = 1
join prefix_mst_position t_position on t_position.id = t_structure.id_position and t_position.status = 1
where
    t_structure.status = 1
    and (t_structure.actived_at is null or t_structure.actived_at <= now())
    and (t_structure.expired_at is null or t_structure.expired_at >= now())
    /* and condition */
