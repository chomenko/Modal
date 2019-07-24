if (module.hot) {
	module.hot.accept();
}

import {App, BaseComponent, SAGA_REDRAW_SNIPPET, Saga} from "Stage"
export const SAGA_MODAL_REQUEST_STARTED = 'SAGA_MODAL_REQUEST_STARTED';

class ModalComponent extends BaseComponent {

	initial() {
		super.initial();
	}

	@Saga(SAGA_MODAL_REQUEST_STARTED)
	public sagaModal(action) {
		const {payload} = action;
		const {content, snippetName, response} = payload;
		const {modal} = response;

		if (content) {
			let resultContent = content;
			let source = $(document).find('#' + snippetName);
			let sourceModalContent = source.find('.modal-dialog');

			if (sourceModalContent.length > 0) {

				let modalSnippetId = content.attr('id');

				let replaceElement = source.find(".modal#" + modalSnippetId).find(".modal-dialog");
				if (replaceElement.length > 0) {
					let modalDialog = $(content.find(".modal-dialog").html());
					replaceElement.html(modalDialog);
					resultContent = modalDialog;
				} else {
					source.append(content);
				}

			} else {
				source.html(content);
			}

			let modalSnippetId = content.attr('id');
			$('#' + modalSnippetId).modal("show");

			for (let modalId in modal) {
				const modalData = modal[modalId];
				if (modalData.close) {
					const selectedModal = $('#' + modalId);
					selectedModal.on("hidden.bs.modal", function () {
						$(this).remove();
					});
				}

				App.store.dispatch({
					type: SAGA_REDRAW_SNIPPET,
					payload: {
						snippetName: snippetName,
						content: resultContent,
						response: payload
					}
				});
			}

		}
	}
}

App.addComponent("ModalComponent", ModalComponent);
