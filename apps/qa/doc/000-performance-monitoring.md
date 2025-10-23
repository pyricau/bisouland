# Performance Monitoring

This guide explains how to monitor and track server-side performance of the legacy application during refactoring.

## Overview

The performance monitoring system:
- Records **server-side execution times** (no network latency)
- Stores metrics in the database for historical analysis
- Provides reports with statistical analysis (avg, median, p95, p99)
- **Disabled by default** - must be explicitly enabled via environment variable

## Quick Reference

```bash
# Common workflow
make benchmark                    # Run before changes
# ... make your changes ...
make benchmark                    # Run after changes
make performance-history          # See when runs happened
# Copy suggested command and run it

# View reports
make performance-report                        # Last 24 hours
make performance-report arg='--hours=168'      # Last week

# Compare with custom threshold
make benchmark-compare arg='--datetime="..." --threshold=10'

# Cleanup
make performance-cleanup arg='--days=30'       # Keep last 30 days
```

## Quick Start

### 1. Enable Performance Recording

Performance recording is **disabled by default**. To enable it locally without modifying committed files:

```bash
# From the monolith directory
cd apps/monolith

# Copy .env to .env.local (if not already done)
cp .env .env.local

# Enable performance recording
sed -i '' 's/PERFORMANCE_RECORDING_ENABLED=false/PERFORMANCE_RECORDING_ENABLED=true/' .env.local

# Restart the containers to apply changes
docker compose down && docker compose up -d
```

This will start recording every page request's execution time to the `performance_metrics` table.

**Why .env.local?**
- `.env` contains default values (committed to git)
- `.env.local` overrides for local development (ignored by git)
- Never commit `.env.local` - it's in `.gitignore`

**Note:** Recording is opt-in to avoid database overhead in production. Only enable when you need to collect performance data.

### 2. Generate Benchmark Data

You can generate performance data in two ways:

**Option A: Run automated benchmark (recommended)**

```bash
# Run 10 iterations (default)
make benchmark

# Run 20 iterations for better statistics
make benchmark arg='20'
```

This runs the smoke tests multiple times (default: 10 iterations) to hit all pages and generate reliable performance statistics, then immediately shows a 1-hour performance report.

**Option B: Use the application manually**

Just browse the application normally. Every page load will record its execution time.

### 3. View Performance Report

```bash
# Show report for last 24 hours (default)
make performance-report

# Show report for last week (168 hours)
make performance-report arg='--hours=168'

# Show report for last 30 days (720 hours)
make performance-report arg='--hours=720'
```

## Understanding the Metrics

The report shows:

- **Samples**: Number of requests recorded
- **Avg**: Average response time
- **Median**: Median response time (50th percentile - less affected by outliers)
- **P95**: 95th percentile (95% of requests were faster than this)
- **P99**: 99th percentile (worst-case performance for 99% of requests)

### Example Output

```
📊 Performance Report (Last 24 hours)
====================================

Page Performance
----------------

+--------------+---------+----------+----------+----------+----------+
| Page         | Samples | Avg      | Median   | P95      | P99      |
+--------------+---------+----------+----------+----------+----------+
| cerveau      | 523     | 125.3 ms | 118.2 ms | 187.4 ms | 245.6 ms |
| construction | 234     | 98.7 ms  | 92.1 ms  | 152.3 ms | 198.2 ms |
| accueil      | 892     | 45.2 ms  | 42.8 ms  | 68.5 ms  | 89.3 ms  |
+--------------+---------+----------+----------+----------+----------+

🐌 Top 5 Slowest Pages (by P95)
-------------------------------

 1. cerveau  P95: 187.4 ms  Avg: 125.3 ms  (523 samples)
 2. construction  P95: 152.3 ms  Avg: 98.7 ms  (234 samples)
 3. techno  P95: 145.8 ms  Avg: 95.2 ms  (189 samples)
```

## Workflow for Refactoring

### 1. Run Initial Benchmark (Before)

```bash
# Run 10 iterations to establish baseline
make benchmark
```

This records performance metrics for all pages and shows a 1-hour report.

### 2. Make Your Changes

Refactor your code, optimize queries, etc.

### 3. Run Benchmark Again (After)

```bash
# Run 10 more iterations with your changes
make benchmark
```

### 4. Compare Results

First, check when your benchmark runs happened:

```bash
# Show benchmark run history
make performance-history
```

This will show all your benchmark runs and suggest a comparison command for the two most recent runs.

**Example output:**
```
📊 Benchmark Run History
========================

+------------------+---------+----------+
| Run Time         | Samples | Duration |
+------------------+---------+----------+
| 2025-10-16 06:06 | 360     | 1s       |
| 2025-10-15 06:12 | 52      | 0s       |
| 2025-10-15 06:04 | 52      | 1s       |
+------------------+---------+----------+

💡 Suggested Comparison
-----------------------

 To compare the two most recent runs:
   make benchmark-compare arg='--datetime="2025-10-15 18:09:26"'
```

Then use the suggested command or customize it:

```bash
# Use the suggested comparison from performance-history
make benchmark-compare arg='--datetime="2025-10-15 18:09:26"'

# Or compare with custom datetime and window
make benchmark-compare arg='--datetime="2024-10-14 15:30:00" --hours=2'
```

**Important:** The datetime should be **between** your two benchmark runs, not at the current time. The command compares data before vs after that datetime.

### Dealing with Measurement Variance

Even with identical code, you'll see some variance due to:
- System noise (other processes, CPU scheduling)
- Database cache effects
- Natural execution time variations

**Solutions:**

```bash
# Option 1: Run more iterations for more stable statistics
make benchmark arg='50'  # Instead of default 10

# Option 2: Increase similarity threshold to filter noise
make benchmark-compare arg='--datetime="..." --threshold=10'

# Option 3: Focus on large changes only
make benchmark-compare arg='--datetime="..." --threshold=15'
```

**Recommended thresholds:**
- **5%** (default): Good for detecting small regressions during refactoring
- **10%**: Better for noisy environments, filters statistical noise
- **15%**: Focus only on significant performance changes

**Example output:**
```
📊 Performance Comparison: Before vs After
==========================================

🟢 Improved
-----------

 * 🟢 cerveau avg: -15.32 ms (-12.5%) p95: -18.2 ms (-13.1%)

🔴 Degraded
-----------

 * 🔴 construction avg: +3.45 ms (+2.8%) p95: +4.1 ms (+3.1%)

⚪ Similar (within ±10%)
-----------------------

 * ⚪ bisous avg: +0.52 ms (+0.4%) p95: +0.63 ms (+0.5%)
```

The section header dynamically updates to show the threshold you're using (e.g., "within ±10%" when using `--threshold=10`).

### Advanced: Custom Iterations

```bash
# Run more iterations for better statistics
make benchmark arg='50'

# Run fewer iterations for quick checks
make benchmark arg='5'
```

### Starting Fresh

If you want to start with clean metrics:

```bash
# Delete all metrics older than 0 days (delete everything)
make performance-cleanup arg='--days=0'

# Then run your benchmarks
make benchmark
# ... make changes ...
make benchmark
make benchmark-compare
```

## Advanced Usage

### Using the Performance Script Directly

You can access all Symfony Console features through the `performance` target:

```bash
# List all available commands
make performance arg='list'

# Get help for a specific command
make performance arg='metrics:report --help'
make performance arg='metrics:compare --help'

# Run any command with custom options
make performance arg='metrics:report --hours=48'
make performance arg='metrics:compare --datetime="2024-10-14 15:30:00" --hours=3 --threshold=10'
make performance arg='metrics:history --days=7'
make performance arg='metrics:prune --days=7'
```

**Available Commands:**
- `metrics:report` - Display performance statistics with tables
- `metrics:compare` - Compare before/after metrics (supports `--threshold`)
- `metrics:history` - Show benchmark run history with suggested comparison
- `metrics:prune` - Clean up old performance data

The `performance` target gives you full access to the Symfony Console application, while the shorthand targets (`performance-report`, `benchmark-compare`, `performance-history`, `performance-cleanup`) provide convenient aliases for common operations.

## Maintenance

### Clean Up Old Data

Performance data can grow large over time. Clean up old metrics:

```bash
# Keep last 24 days, delete older data (default)
make performance-cleanup

# Keep last 30 days, delete older data
make performance-cleanup arg='--days=30'
```

### Disable Recording

To stop recording (useful after benchmarking or to reduce database load):

```bash
# Disable performance recording in .env.local
sed -i '' 's/PERFORMANCE_RECORDING_ENABLED=true/PERFORMANCE_RECORDING_ENABLED=false/' .env.local

# Or delete .env.local entirely to use defaults from .env
rm .env.local

# Restart containers
docker compose restart
```

**Note:** Never edit `.env` directly for temporary changes - use `.env.local` instead.

## Technical Details

### How It Works

1. The monolith application measures execution time for each page request
2. Performance data is recorded to the `performance_metrics` table (when `PERFORMANCE_RECORDING_ENABLED=true`)
3. `bin/benchmark.php` runs smoke tests multiple times to generate consistent performance data
4. `bin/performance.php` is a Symfony Console application with four commands:
   - `metrics:report` - Display performance statistics with tables
   - `metrics:compare` - Compare before/after metrics with colored output
   - `metrics:history` - Show benchmark run history and suggest comparison commands
   - `metrics:prune` - Clean up old performance data

### Database Schema

```sql
CREATE TABLE performance_metrics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    timestamp INT NOT NULL,
    operation VARCHAR(50) NOT NULL,
    duration FLOAT NOT NULL,
    INDEX idx_operation_timestamp (operation, timestamp),
    INDEX idx_timestamp (timestamp)
);
```

### Why This Approach?

- **No network latency**: Measures actual PHP execution time
- **Minimal overhead**: Simple database insert, fails silently
- **Real user data**: Captures actual production performance
- **Disabled by default**: Opt-in to avoid unnecessary database load
- **Safe**: Recording failures never break the application
- **Professional CLI**: Symfony Console provides tables, colors, and help documentation
- **Easy to use**: Named options (--hours, --datetime) are self-documenting

## Preserving Performance Data During DB Reset

If you need to reset the database during refactoring (e.g., testing migrations) but want to keep performance metrics for comparison:

```bash
# From the monolith directory
./bin/db-reset.sh --keep-performance-metrics
```

This will:
1. Backup `performance_metrics` table
2. Drop and recreate the database
3. Load the schema
4. Restore the performance metrics

## Troubleshooting

### "No comparison data available"

This usually means the datetime or hours window doesn't match your data. Solutions:

```bash
# Step 1: Check when your benchmarks ran
make performance-history

# Step 2: Use the suggested command (includes correct datetime and hours)
# Copy the command from the "💡 Suggested Comparison" section
```

**Common causes:**
- **Datetime is too recent**: Using `--datetime=now` when both benchmarks are in the past
- **Window too small**: Default 1-hour window may miss your benchmarks if they're far apart
- **Solution**: Always use `performance-history` to get the right parameters

### Too many false positives/negatives

If you see improvements and degradations with no code changes:

```bash
# Increase threshold to filter statistical noise
make benchmark-compare arg='--datetime="..." --threshold=10'

# Or run more iterations for stable statistics
make benchmark arg='50'
```

## Tips

1. **Focus on P95/P99**: These represent worst-case user experience, not averages
2. **Use performance-history**: Always run `make performance-history` first to get the correct comparison command
3. **Adjust threshold for noise**: Use `--threshold=10` or higher if you see too many false positives
4. **Run more iterations**: `make benchmark arg='50'` produces more stable statistics than the default 10
5. **Watch for regressions**: If P95 increases by >15% after a change, investigate
6. **Optimize hot paths**: Focus on frequently accessed pages first
7. **Database queries**: Most slowness in legacy PHP comes from DB queries
8. **Preserve metrics**: Use `--keep-performance-metrics` when resetting the database to maintain history
