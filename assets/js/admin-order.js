(($) => {
  const list = $('[data-gemonio-order-list]');
  if (!list.length || !window.gemonioOrder) return;

  const message = $('[data-gemonio-order-message]');
  let timer;

  const save = () => {
    const order = list.children('[data-id]').map(function () {
      return $(this).data('id');
    }).get();

    message.text('Saving…');

    $.post(gemonioOrder.ajaxUrl, {
      action: 'gemonio_save_section_order',
      nonce: gemonioOrder.nonce,
      order,
    }).done((response) => {
      message.text(response?.data?.message || 'Saved.');
      clearTimeout(timer);
      timer = setTimeout(() => message.text(''), 1800);
    }).fail(() => {
      message.text('Could not save the order.');
    });
  };

  list.sortable({
    axis: 'y',
    handle: '.dashicons-menu',
    placeholder: 'gemonio-order-placeholder',
    update: save,
  });
})(jQuery);
