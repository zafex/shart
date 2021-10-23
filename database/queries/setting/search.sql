select
	setting.id,
	setting.identity,
	setting.label,
	setting.description,
	setting.status,
	setting.created_at,
	setting.created_by,
	setting.updated_at,
	setting.updated_by,
	count(item.id) as count_items
from prefix_sys_setting setting
left join prefix_sys_setting_item item on item.id_setting = setting.id and item.status = 1
/* where condition */
group by
	setting.id,
	setting.identity,
	setting.label,
	setting.description,
	setting.status,
	setting.created_at,
	setting.created_by,
	setting.updated_at,
	setting.updated_by