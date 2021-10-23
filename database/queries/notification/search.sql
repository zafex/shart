select
	notification.id,
	notification.action,
	message.subject,
	message.body,
	message.sender,
	notification.status,
	notification.created_at,
	notification.created_by
from prefix_sys_message message
join prefix_sys_notification notification on notification.id_message = message.id
where message.status = 1
/* and condition */