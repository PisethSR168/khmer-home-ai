import 'package:flutter/material.dart';
import '../../constants/theme_constants.dart';
import '../../models/viewing_request_model.dart';
import '../../services/viewing_service.dart';

class MyViewingsScreen extends StatefulWidget {
  const MyViewingsScreen({Key? key}) : super(key: key);

  @override
  State<MyViewingsScreen> createState() => _MyViewingsScreenState();
}

class _MyViewingsScreenState extends State<MyViewingsScreen> {
  final ViewingService _viewingService = ViewingService();
  List<ViewingRequestModel> _requests = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadViewings();
  }

  Future<void> _loadViewings() async {
    setState(() => _isLoading = true);
    try {
      final list = await _viewingService.getViewingRequests();
      setState(() => _requests = list);
    } catch (_) {
    } finally {
      setState(() => _isLoading = false);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('My Viewing Visits'),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _requests.isEmpty
              ? const Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.calendar_month_outlined, size: 48, color: AppColors.textMuted),
                      SizedBox(height: 12),
                      Text(
                        'No viewing appointments booked',
                        style: TextStyle(fontWeight: FontWeight.bold, fontSize: 15),
                      ),
                      SizedBox(height: 4),
                      Text(
                        'Browse rooms and request a visit to see them here.',
                        style: TextStyle(color: AppColors.textMuted, fontSize: 12),
                      ),
                    ],
                  ),
                )
              : RefreshIndicator(
                  onRefresh: _loadViewings,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: _requests.length,
                    itemBuilder: (context, index) {
                      final item = _requests[index];
                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(color: AppColors.border),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Row(
                              mainAxisAlignment: MainAxisAlignment.spaceBetween,
                              children: [
                                _buildStatusBadge(item.status),
                                Text(
                                  '${item.preferredDate} at ${item.preferredTime}',
                                  style: const TextStyle(
                                    color: AppColors.textMuted,
                                    fontSize: 11,
                                    fontWeight: FontWeight.bold,
                                  ),
                                ),
                              ],
                            ),
                            const SizedBox(height: 10),
                            Text(
                              item.property?.title ?? 'Rental Property',
                              style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              'Landlord: ${item.landlord?.name ?? 'Property Owner'} • Phone: ${item.contactPhone}',
                              style: const TextStyle(color: AppColors.textMuted, fontSize: 12),
                            ),
                            if (item.renterNote != null && item.renterNote!.isNotEmpty)
                              Padding(
                                padding: const EdgeInsets.only(top: 8),
                                child: Text(
                                  'Note: "${item.renterNote}"',
                                  style: TextStyle(color: Colors.grey.shade700, fontSize: 11, fontStyle: FontStyle.italic),
                                ),
                              ),
                          ],
                        ),
                      );
                    },
                  ),
                ),
    );
  }

  Widget _buildStatusBadge(String status) {
    Color bg = Colors.grey.shade100;
    Color fg = Colors.grey.shade800;

    if (status == 'confirmed') {
      bg = AppColors.primaryLight;
      fg = AppColors.primaryDark;
    } else if (status == 'pending') {
      bg = Colors.amber.shade50;
      fg = Colors.amber.shade800;
    }

    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(color: bg, borderRadius: BorderRadius.circular(6)),
      child: Text(
        status.toUpperCase(),
        style: TextStyle(color: fg, fontSize: 10, fontWeight: FontWeight.bold),
      ),
    );
  }
}
