select count(notification.id)
from prefix_sys_message message
join prefix_sys_notification notification on notification.id_message = message.id
where message.status = 1
/* and condition */