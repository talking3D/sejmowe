SELECT DATE(timestamp), COUNT(id) FROM `sentyment` GROUP BY DATE(timestamp);
SELECT COUNT(*) FROM `sentyment`;
SELECT sentyment, count(sentyment) FROM `sentyment` GROUP BY sentyment;