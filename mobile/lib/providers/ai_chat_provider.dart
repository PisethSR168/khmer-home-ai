import 'package:flutter/material.dart';
import '../services/ai_service.dart';

class ChatMessage {
  final String text;
  final bool isUser;
  final List<dynamic>? suggestedRooms;

  ChatMessage({
    required this.text,
    required this.isUser,
    this.suggestedRooms,
  });
}

class AiChatProvider extends ChangeNotifier {
  final AiService _aiService = AiService();

  final List<ChatMessage> _messages = [
    ChatMessage(
      text:
          "Suasdei! I am your Khmer Home AI Rental Assistant in Siem Reap. I can help you find peaceful garden studios in Sala Kamreuk, rooms in Wat Bo, or apartments near Pub Street, explain Cambodia lease deposits, and check electricity rates (\$0.20/kWh standard). What type of room or budget are you looking for in Siem Reap?",
      isUser: false,
    ),
  ];

  bool _isLoading = false;

  List<ChatMessage> get messages => _messages;
  bool get isLoading => _isLoading;

  Future<void> sendMessage(String text) async {
    if (text.trim().isEmpty || _isLoading) return;

    _messages.add(ChatMessage(text: text, isUser: true));
    _isLoading = true;
    notifyListeners();

    try {
      final res = await _aiService.chatWithAssistant(text);
      _messages.add(ChatMessage(
        text: res['reply'] ?? 'Here are matching accommodations in Phnom Penh.',
        isUser: false,
        suggestedRooms: res['suggested_rooms'],
      ));
    } catch (e) {
      _messages.add(ChatMessage(
        text: "I can help you explore rooms starting from \$140/mo in Tuol Kouk to luxury studios in BKK1. Please try asking again!",
        isUser: false,
      ));
    } finally {
      _isLoading = false;
      notifyListeners();
    }
  }

  void clearChat() {
    _messages.clear();
    _messages.add(ChatMessage(
      text: "Chat reset. How can I assist you with your rental search today?",
      isUser: false,
    ));
    notifyListeners();
  }
}
