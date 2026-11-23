import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import 'constants/theme_constants.dart';
import 'providers/ai_chat_provider.dart';
import 'providers/auth_provider.dart';
import 'providers/property_provider.dart';
import 'screens/splash_screen.dart';
import 'services/api_service.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  await ApiService().initToken();

  runApp(const KhmerHomeApp());
}

class KhmerHomeApp extends StatelessWidget {
  const KhmerHomeApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => PropertyProvider()),
        ChangeNotifierProvider(create: (_) => AiChatProvider()),
      ],
      child: MaterialApp(
        title: 'Room SR - បន្ទប់ជួល សៀមរាប',
        debugShowCheckedModeBanner: false,
        theme: AppTheme.lightTheme,
        home: const SplashScreen(),
      ),
    );
  }
}
