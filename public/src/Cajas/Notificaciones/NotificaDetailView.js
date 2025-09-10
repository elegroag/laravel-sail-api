import { ModelView } from "@/Common/ModelView";

export default class NotificaDetailView extends ModelView {
	constructor(options={}) {
		super(options);
		this.template = _.template(document.getElementById('notificationDetail').innerHTML);
	}
}
