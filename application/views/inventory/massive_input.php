<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Specific styles for massive input layout */
.input-section {
    flex: 1;
    min-width: 280px; /* Adjusted min-width for better textarea display */
    padding: 0 16px;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}
.input-section:not(:last-child) {
    border-right: 1px solid #eee;
    padding-right: 16px;
}
.input-section h4 {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 10px;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 4px;
    font-weight: bold;
    font-size: 13px;
}
.input, .select, textarea {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 14px;
    box-sizing: border-box; /* Ensure padding doesn't increase width */
}
textarea {
    min-height: 150px; /* Larger textarea for multiple inputs */
    resize: vertical;
}
.btn-submit {
    padding: 8px 16px;
    min-width: 100px;
    font-size: 14px;
    margin-top: 10px;
    align-self: flex-start;
}
.result-message {
    margin-top: 10px;
    padding: 8px;
    border-radius: 4px;
    font-size: 13px;
}
.result-message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.result-message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #1677ff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    vertical-align: middle;
    margin-left: 8px;
}
</style>

<!-- Container -->
<div class="container-fixed">
  <div class="card min-w-full">
    <div class="card-header flex items-center justify-between">
      <div class="flex items-center gap-2">
        <h3 class="modal-title">Massive Inventory Input</h3>
      </div>
    </div>
    <div class="card-body" style="display: flex; gap: 24px; justify-content: space-between; align-items: flex-start; flex-wrap: wrap;">
      <!-- Input In Section -->
      <div class="input-section">
        <h4>Input In</h4>
        <div class="form-group">
          <label for="in_qc_status">QC Status *</label>
          <select id="in_qc_status" class="select">
            <option value="0">LN</option>
            <option value="1">DN</option>
          </select>
        </div>
        <div class="form-group">
          <label for="in_serial_numbers">Serial Numbers *</label>
          <textarea id="in_serial_numbers" placeholder="Enter serial numbers, one per line or separated by tabs"></textarea>
          <small style="color: #666; font-size: 12px;">Enter multiple serial numbers, separated by new lines or tabs.</small>
        </div>
        <button class="btn btn-primary btn-submit" onclick="submitMassiveInput('in')">
          Submit In <span id="in_loading_spinner" class="loading-spinner" style="display:none;"></span>
        </button>
        <div id="in_result_summary" class="result-message" style="display:none;"></div>
      </div>

      <!-- Input Move Section -->
      <div class="input-section">
        <h4>Input Move</h4>
        <div class="form-group">
          <label for="move_location">Lokasi Tujuan *</label>
          <select id="move_location" class="select">
            <option value="">-- Pilih Lokasi Tujuan --</option>
            <option value="Lantai 2">🏢 Lantai 2</option>
            <option value="Bang Toni">👨‍💼 Bang Toni</option>
            <option value="Om Bob">👨‍💼 Om Bob</option>
            <option value="Rekanan">🤝 Rekanan</option>
            <option value="LN">🏭 LN</option>
            <option value="ECBS">🏭 ECBS</option>
            <option value="LN Office">🏢 LN Office</option>
            <option value="Lantai 1">🏢 Lantai 1</option>
            <option value="Unknown">❓ Unknown</option>
          </select>
        </div>
        <div class="form-group">
          <label for="move_serial_numbers">Serial Numbers *</label>
          <textarea id="move_serial_numbers" placeholder="Enter serial numbers, one per line or separated by tabs"></textarea>
          <small style="color: #666; font-size: 12px;">Serial numbers must already exist in the database.</small>
        </div>
        <button class="btn btn-primary btn-submit" onclick="submitMassiveInput('move')">
          Submit Move <span id="move_loading_spinner" class="loading-spinner" style="display:none;"></span>
        </button>
        <div id="move_result_summary" class="result-message" style="display:none;"></div>
      </div>

      <!-- Input Out Section -->
      <div class="input-section">
        <h4>Input Out</h4>
        <div class="form-group">
          <label for="out_serial_numbers">Serial Numbers *</label>
          <textarea id="out_serial_numbers" placeholder="Enter serial numbers, one per line or separated by tabs"></textarea>
          <small style="color: #666; font-size: 12px;">Serial numbers must already exist in the database.</small>
        </div>
        <button class="btn btn-primary btn-submit" onclick="submitMassiveInput('out')">
          Submit Out <span id="out_loading_spinner" class="loading-spinner" style="display:none;"></span>
        </button>
        <div id="out_result_summary" class="result-message" style="display:none;"></div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
// Global config from PHP
var config = <?php echo json_encode($config); ?>;

async function submitMassiveInput(type) {
    let serialNumbersText;
    let qcStatus = null;
    let location = null;
    let resultSummaryElement;
    let loadingSpinnerElement;

    if (type === 'in') {
        serialNumbersText = document.getElementById('in_serial_numbers').value;
        qcStatus = document.getElementById('in_qc_status').value;
        resultSummaryElement = document.getElementById('in_result_summary');
        loadingSpinnerElement = document.getElementById('in_loading_spinner');
    } else if (type === 'out') {
        serialNumbersText = document.getElementById('out_serial_numbers').value;
        resultSummaryElement = document.getElementById('out_result_summary');
        loadingSpinnerElement = document.getElementById('out_loading_spinner');
    } else if (type === 'move') {
        serialNumbersText = document.getElementById('move_serial_numbers').value;
        location = document.getElementById('move_location').value;
        resultSummaryElement = document.getElementById('move_result_summary');
        loadingSpinnerElement = document.getElementById('move_loading_spinner');
    }

    // Show loading spinner
    loadingSpinnerElement.style.display = 'inline-block';
    resultSummaryElement.style.display = 'none'; // Hide previous summary

    const serialNumbers = serialNumbersText.split(/[\n\t]+/).map(s => s.trim()).filter(s => s !== '');

    if (serialNumbers.length === 0) {
        alert('⚠️ Please enter at least one serial number.');
        loadingSpinnerElement.style.display = 'none';
        return;
    }

    if (type === 'move' && !location) {
        alert('⚠️ Lokasi tujuan tidak boleh kosong!');
        loadingSpinnerElement.style.display = 'none';
        return;
    }

    let successCount = 0;
    let failCount = 0;
    const failedSerials = [];
    const url = config.url_menu + 'input_process';

    for (const sn of serialNumbers) {
        let data = { type: type, serial_number: sn };
        if (type === 'in') {
            data.qc_status = qcStatus;
        } else if (type === 'move') {
            data.location = location;
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (result.success) {
                successCount++;
            } else {
                failCount++;
                failedSerials.push(`${sn}: ${result.message}`);
            }
        } catch (error) {
            failCount++;
            failedSerials.push(`${sn}: Network error or invalid response (${error.message})`);
        }
    }

    // Hide loading spinner
    loadingSpinnerElement.style.display = 'none';

    // Display summary
    let summaryMessage = `Processing complete: ${successCount} successful, ${failCount} failed.`;
    resultSummaryElement.className = 'result-message';
    if (failCount === 0) {
        resultSummaryElement.classList.add('success');
        // Clear textarea only on full success
        if (type === 'in') document.getElementById('in_serial_numbers').value = '';
        else if (type === 'out') document.getElementById('out_serial_numbers').value = '';
        else if (type === 'move') document.getElementById('move_serial_numbers').value = '';
    } else {
        resultSummaryElement.classList.add('error');
        summaryMessage += '\n\nFailed serial numbers:\n' + failedSerials.join('\n');
        // Kembalikan SN yang gagal ke textarea input
        const failedSNs = failedSerials.map(f => f.split(':')[0]);
        if (type === 'in') document.getElementById('in_serial_numbers').value = failedSNs.join('\n');
        else if (type === 'out') document.getElementById('out_serial_numbers').value = failedSNs.join('\n');
        else if (type === 'move') document.getElementById('move_serial_numbers').value = failedSNs.join('\n');
    }
    resultSummaryElement.innerText = summaryMessage;
    resultSummaryElement.style.display = 'block';
}

// No specific onload function needed for this page, as it's purely input.
// The styles are embedded directly.
</script>
