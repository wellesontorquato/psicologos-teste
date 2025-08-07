import 'package:flutter/material.dart';

class LoadingOverlay {
  static final _overlayEntry = ValueNotifier<OverlayEntry?>(null);

  static void show(BuildContext context, {String? mensagem}) {
    if (_overlayEntry.value != null) return;

    final overlay = OverlayEntry(
      builder: (_) => Stack(
        children: [
          ModalBarrier(
            color: Colors.black.withOpacity(0.3),
            dismissible: false,
          ),
          const Center(
            child: CircularProgressIndicator(),
          ),
        ],
      ),
    );

    _overlayEntry.value = overlay;
    Overlay.of(context).insert(overlay);
  }

  static void hide() {
    _overlayEntry.value?.remove();
    _overlayEntry.value = null;
  }
}
