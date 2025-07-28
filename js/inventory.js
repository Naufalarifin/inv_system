/**
 * Universal Inventory Management System
 * File JavaScript universal untuk inv_ecbs.php dan inv_ecct.php
 *
 * Deteksi otomatis berdasarkan INVENTORY_TYPE yang di-set di PHP
 */

// =================== GLOBAL VARIABLES ===================
var currentTable = ""
var currentType = "app" // app atau osc
var inventoryType = "" // 'ECBS' atau 'ECCT'
var CONFIG = window.CONFIG || {} // Declare CONFIG variable
var inputMode = "singular" // "singular" atau "massive"

// Debug: log CONFIG object
console.log("CONFIG:", CONFIG)

// =================== INITIALIZATION ===================
document.addEventListener("DOMContentLoaded", () => {
  // Deteksi inventory type dari global variable yang di-set di PHP
  inventoryType = window.INVENTORY_TYPE || "ECBS"

  // Set current table berdasarkan inventory type
  if (inventoryType === "ECBS") {
    currentTable = "ecbs"
  } else if (inventoryType === "ECCT") {
    currentTable = "ecct"
  }

  // Initialize
  initializeEventListeners()
  renderToolbar()
  showMainData()
  showInputTab("in")
})

// =================== MODAL UTILITIES ===================
function openModal(modalId) {
  const overlay = document.getElementById("modal_overlay")
  const modal = document.getElementById(modalId)

  if (overlay && modal) {
    overlay.style.display = "block"
    modal.style.display = "block"

    // Initialize auto-submit listeners when modal opens
    if (modalId === "modal_input_all") {
      initializeAutoSubmitListeners()
      renderInputMode()
    }
  }
}

function closeModal(modalId) {
  const overlay = document.getElementById("modal_overlay")
  const modal = document.getElementById(modalId)

  if (overlay && modal) {
    overlay.style.display = "none"
    modal.style.display = "none"
  }

  if (modalId === "modal_input_all") {
    resetInputForm()
  }
}

function resetInputForm() {
  // Reset singular inputs
  document.getElementById("in_serial_number").value = ""
  document.getElementById("out_serial_number").value = ""
  document.getElementById("move_serial_number").value = ""
  document.getElementById("move_location").value = ""

  // Reset massive inputs
  document.getElementById("in_serial_numbers_massive").value = ""
  document.getElementById("out_serial_numbers_massive").value = ""
  document.getElementById("move_serial_numbers_massive").value = ""

  // Clear result messages
  const resultDivs = ["in_result_message", "out_result_message", "move_result_message"]
  resultDivs.forEach((id) => {
    const div = document.getElementById(id)
    if (div) {
      div.style.display = "none"
      div.innerText = ""
    }
  })
}

// =================== INPUT MODE SWITCHING ===================
function switchInput(mode) {
  inputMode = mode
  renderInputMode()
}

function renderInputMode() {
  // Update toggle buttons
  const singularBtn = document.getElementById("btn_singular")
  const massiveBtn = document.getElementById("btn_massive")

  if (singularBtn && massiveBtn) {
    if (inputMode === "singular") {
      singularBtn.className = "input-mode-btn active"
      massiveBtn.className = "input-mode-btn"
    } else {
      singularBtn.className = "input-mode-btn"
      massiveBtn.className = "input-mode-btn active"
    }
  }

  // Show/hide appropriate input elements based on current tab and mode
  const currentTab = document.querySelector(".input-tab-btn.active").id.replace("tabBtn_", "")
  updateInputDisplay(currentTab)
}

function updateInputDisplay(tab) {
  const singularContainer = document.getElementById(`${tab}_singular_container`)
  const massiveContainer = document.getElementById(`${tab}_massive_container`)

  if (singularContainer && massiveContainer) {
    if (inputMode === "singular") {
      singularContainer.style.display = "block"
      massiveContainer.style.display = "none"
    } else {
      singularContainer.style.display = "none"
      massiveContainer.style.display = "block"
    }
  }
}

// =================== AUTO SUBMIT LISTENERS ===================
function initializeAutoSubmitListeners() {
  // Event listener untuk input IN
  const inSerialInput = document.getElementById("in_serial_number")
  if (inSerialInput) {
    // Remove existing listeners to prevent duplicates
    inSerialInput.removeEventListener("input", handleInSerialInput)
    inSerialInput.addEventListener("input", handleInSerialInput)
  }

  // Event listener untuk input OUT
  const outSerialInput = document.getElementById("out_serial_number")
  if (outSerialInput) {
    outSerialInput.removeEventListener("input", handleOutSerialInput)
    outSerialInput.addEventListener("input", handleOutSerialInput)
  }

  // Event listener untuk input MOVE
  const moveSerialInput = document.getElementById("move_serial_number")
  if (moveSerialInput) {
    moveSerialInput.removeEventListener("input", handleMoveSerialInput)
    moveSerialInput.addEventListener("input", handleMoveSerialInput)
  }
}

function handleInSerialInput(event) {
  const serialNumber = event.target.value.trim()
  if (serialNumber.length === 15) {
    // Validate before auto-submit
    const resultDiv = document.getElementById("in_result_message")
    if (validateSerialNumber(serialNumber, "in", resultDiv)) {
      submitInput("in")
    }
  }
}

function handleOutSerialInput(event) {
  const serialNumber = event.target.value.trim()
  if (serialNumber.length === 15) {
    // Validate before auto-submit
    const resultDiv = document.getElementById("out_result_message")
    if (validateSerialNumber(serialNumber, "out", resultDiv)) {
      submitInput("out")
    }
  }
}

function handleMoveSerialInput(event) {
  const serialNumber = event.target.value.trim()
  if (serialNumber.length === 15) {
    // Validate before auto-submit
    const resultDiv = document.getElementById("move_result_message")
    if (validateSerialNumber(serialNumber, "move", resultDiv)) {
      // Check if location is selected for move
      const location = document.getElementById("move_location").value
      if (!location) {
        resultDiv.innerText = "Pilih lokasi tujuan terlebih dahulu!"
        resultDiv.className = "input-result-message error"
        resultDiv.style.display = "block"
        return
      }
      submitInput("move")
    }
  }
}

// =================== EVENT LISTENERS ===================
function initializeEventListeners() {
  const overlay = document.getElementById("modal_overlay")

  // Klik overlay untuk menutup modal
  overlay.addEventListener("click", (event) => {
    if (event.target === overlay) {
      const modals = ["modal_filter_ecbs", "modal_filter_ecct", "modal_filter_item", "modal_input_all"]
      modals.forEach((modalId) => {
        const modal = document.getElementById(modalId)
        if (modal && modal.style.display === "block") {
          closeModal(modalId)
        }
      })
    }
  })

  // ESC key untuk menutup modal
  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") {
      const modals = ["modal_filter_ecbs", "modal_filter_ecct", "modal_filter_item", "modal_input_all"]
      modals.forEach((modalId) => {
        const modal = document.getElementById(modalId)
        if (modal && modal.style.display === "block") {
          closeModal(modalId)
        }
      })
    }
  })
}

// =================== COMMON UTILITIES ===================
function loadData(link) {
  if (typeof window.$ !== "undefined") {
    // Use window.$ to avoid undeclared variable error
    window.$("#show_data").load(link)
  } else {
    fetch(link)
      .then((response) => response.text())
      .then((data) => {
        document.getElementById("show_data").innerHTML = data
      })
      .catch((error) => {
        document.getElementById("show_data").innerHTML =
          '<div style="padding: 20px; text-align: center; color: red;">Error loading data</div>'
      })
  }
}

function showLoading() {
  return '<div style="text-align: center; padding: 40px;"><div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #1677ff; border-radius: 50%; animation: spin 1s linear infinite;"></div><p style="margin-top: 10px;">Loading data...</p></div>'
}

// =================== SERIAL NUMBER VALIDATION ===================
function validateSerialNumber(serialNumber, type, resultDiv) {
  const expectedChar = inventoryType === "ECBS" ? "S" : "T"
  const deviceName = inventoryType

  if (serialNumber.length < 6 || serialNumber.charAt(5).toUpperCase() !== expectedChar) {
    if (resultDiv) {
      resultDiv.innerText = `Masukan ${deviceName}!`
      resultDiv.className = "input-result-message error"
      resultDiv.style.display = "block"
    }
    return false
  }
  return true
}

// =================== TABLE SWITCHING ===================
function switchTable(type) {
  currentTable = type
  renderToolbar()

  if (inventoryType === "ECBS") {
    if (type === "ecbs") {
      showMainData()
    } else {
      showDataActivity()
    }
    document.getElementById("btn_ecbs").className =
      "btn btn-sm " + (currentTable === "ecbs" ? "btn-primary" : "btn-light")
    document.getElementById("btn_activity").className =
      "btn btn-sm " + (currentTable === "activity" ? "btn-primary" : "btn-light")
  } else if (inventoryType === "ECCT") {
    if (type === "ecct") {
      showMainData()
    } else {
      showDataAllItem()
    }
    document.getElementById("btn_ecct").className =
      "btn btn-sm " + (currentTable === "ecct" ? "btn-primary" : "btn-light")
    document.getElementById("btn_allitem").className =
      "btn btn-sm " + (currentTable === "allitem" ? "btn-primary" : "btn-light")
  }
}

// =================== TOOLBAR RENDERING ===================
function renderToolbar() {
  var toolbarLeft = ""
  var toolbarRight = ""

  const isMainTable =
    (inventoryType === "ECBS" && currentTable === "ecbs") || (inventoryType === "ECCT" && currentTable === "ecct")

  if (isMainTable) {
    // Untuk main table (ECBS/ECCT), semua di kanan
    toolbarRight += '<div class="btn-group mr-2">'
    toolbarRight +=
      '<button id="btn_app" class="btn btn-sm ' +
      (currentType === "app" ? "btn-primary" : "btn-light") +
      '" onclick="switchMainType(\'app\')">APP</button>'
    toolbarRight +=
      '<button id="btn_osc" class="btn btn-sm ' +
      (currentType === "osc" ? "btn-primary" : "btn-light") +
      '" onclick="switchMainType(\'osc\')">OSC</button>'
    toolbarRight += "</div>"
    toolbarRight +=
      '<a class="btn btn-sm btn-icon-lg btn-light" onclick="showMainData(\'export\');" style="margin-left:4px;"><i class="ki-filled ki-exit-down !text-base"></i>Export</a>'
  } else {
    // Untuk activity/allitem, tombol Input di kiri
    toolbarLeft +=
      '<button class="btn btn-sm" style="background: #28a745; color: white;" onclick="openModal(\'modal_input_all\')" id="input_btn" type="button">Input</button>'

    // Search, Filter, dan Export di kanan
    toolbarRight += '<div class="input-group input-sm">'
    toolbarRight +=
      '<input class="input input-sm" placeholder="Search" type="text" id="key_activity" onkeyup="if(event.key === \'Enter\'){showSecondaryData();}" />'
    toolbarRight += '<span class="btn btn-light btn-sm" onclick="openModal(\'modal_filter_item\')">Filter</span>'
    toolbarRight += '<span class="btn btn-primary btn-sm" onclick="showSecondaryData();">Search</span>'
    toolbarRight += "</div>"
    toolbarRight +=
      '<a class="btn btn-sm btn-icon-lg btn-light" onclick="showSecondaryData(\'export\');" style="margin-left:4px;"><i class="ki-filled ki-exit-down !text-base"></i>Export</a>'
  }

  // Update kedua div
  document.getElementById("toolbar_left").innerHTML = toolbarLeft
  document.getElementById("toolbar_right").innerHTML = toolbarRight
}

// =================== TYPE SWITCHING ===================
function switchMainType(type) {
  currentType = type
  renderToolbar()
  showMainData()
}

// Alias functions untuk backward compatibility
function switchEcbsType(type) {
  switchMainType(type)
}
function switchEcctType(type) {
  switchMainType(type)
}

// =================== DATA LOADING FUNCTIONS ===================
function showMainData(page = 1) {
  const loading = showLoading()
  if (page !== "export") document.getElementById("show_data").innerHTML = loading

  var val = "?"
  val += "&type=" + currentType

  const deviceType = inventoryType.toLowerCase()

  if (page === "export") {
    var linkExport = CONFIG.urlMenu + `data/data_inv_${deviceType}_` + currentType + "_export" + val // Rename variable to avoid redeclaration
    window.open(linkExport, "_blank").focus()
    return
  }

  val += "&p=" + page
  var linkShow = CONFIG.urlMenu + `data/data_inv_${deviceType}_` + currentType + "_show" + val // Rename variable to avoid redeclaration

  loadData(linkShow)
}

function showSecondaryData(page = 1) {
  const loading = showLoading()
  if (page !== "export") document.getElementById("show_data").innerHTML = loading

  var val = "?"
  const fields = [
    "key_activity",
    "dvc_size",
    "dvc_col",
    "dvc_qc",
    "dvc_type",
    "in_date_from",
    "in_date_to",
    "move_date_from",
    "move_date_to",
    "out_date_from",
    "out_date_to",
    "loc_move",
    "sort_by",
    "data_view_item",
    "activity",
  ]

  fields.forEach((field) => {
    var element = document.getElementById(field)
    if (element) {
      val += "&" + field + "=" + encodeURIComponent(element.value)
    }
  })

  // Tambahkan context berdasarkan inventory type
  val += "&context=inv_" + inventoryType.toLowerCase()

  if (page === "export") {
    var linkExport = CONFIG.urlMenu + "data/data_item_export" + val // Rename variable to avoid redeclaration
    window.open(linkExport, "_blank").focus()
    return
  }

  val += "&p=" + page

  // Tentukan link berdasarkan inventory type
  var linkPath = inventoryType === "ECBS" ? "data_item_show_ecbs" : "data_item_show"
  var linkShow = CONFIG.urlMenu + "data/" + linkPath + val // Rename variable to avoid redeclaration

  loadData(linkShow)
}

// Alias functions untuk backward compatibility
function showDataEcbs(page) {
  showMainData(page)
}
function showDataEcct(page) {
  showMainData(page)
}
function showDataActivity(page) {
  showSecondaryData(page)
}
function showDataAllItem(page) {
  showSecondaryData(page)
}

// =================== PAGINATION HANDLING ===================
function handlePagination(page) {
  const isMainTable =
    (inventoryType === "ECBS" && currentTable === "ecbs") || (inventoryType === "ECCT" && currentTable === "ecct")

  if (isMainTable) {
    showMainData(page)
  } else {
    showSecondaryData(page)
  }
}

function getCurrentTableFunction() {
  const isMainTable =
    (inventoryType === "ECBS" && currentTable === "ecbs") || (inventoryType === "ECCT" && currentTable === "ecct")

  if (isMainTable) {
    return inventoryType === "ECBS" ? "showDataEcbs" : "showDataEcct"
  } else {
    return inventoryType === "ECBS" ? "showDataActivity" : "showDataAllItem"
  }
}

// =================== INPUT TAB FUNCTIONS ===================
function showInputTab(tab) {
  // Hide all tab contents
  document.querySelectorAll(".input-tab-content").forEach((el) => {
    el.style.display = "none"
  })
  // Remove active from all tab buttons
  document.querySelectorAll(".input-tab-btn").forEach((btn) => {
    btn.classList.remove("active")
  })
  // Show selected tab content
  document.getElementById("inputTab_" + tab).style.display = "flex"
  // Set active tab button
  document.getElementById("tabBtn_" + tab).classList.add("active")

  // Update input display based on current mode
  updateInputDisplay(tab)

  // Re-initialize auto-submit listeners when switching tabs
  setTimeout(() => {
    initializeAutoSubmitListeners()
  }, 100)
}

// =================== INPUT SUBMISSION ===================
function submitInput(type) {
  if (inputMode === "massive") {
    submitMassiveInput(type)
    return
  }

  let data = {}
  const url = CONFIG.urlMenu + "input_process"
  let resultDiv = ""
  let serialNumber = ""

  if (type === "in") {
    serialNumber = document.getElementById("in_serial_number").value.trim()
    data = {
      type: "in",
      serial_number: serialNumber,
      qc_status: document.getElementById("in_qc_status").value,
      // Untuk singular input, user_date tidak disediakan, sehingga inv_in akan menjadi current timestamp
    }
    resultDiv = document.getElementById("in_result_message")
  } else if (type === "out") {
    serialNumber = document.getElementById("out_serial_number").value.trim()
    data = {
      type: "out",
      serial_number: serialNumber,
    }
    resultDiv = document.getElementById("out_result_message")
  } else if (type === "move") {
    serialNumber = document.getElementById("move_serial_number").value.trim()
    data = {
      type: "move",
      serial_number: serialNumber,
      location: document.getElementById("move_location").value,
    }
    resultDiv = document.getElementById("move_result_message")
  }

  fetch(url, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((result) => {
      if (resultDiv) {
        resultDiv.innerText = result.message
        if (result.success) {
          resultDiv.className = "input-result-message success"
          if (currentTable === "allitem") {
            showDataAllItem()
          } else if (currentTable === "ecct") {
            showDataEcct()
          } else if (currentTable === "ecbs") {
            showDataEcbs()
          } else if (currentTable === "activity") {
            showDataActivity()
          }
        } else {
          resultDiv.className = "input-result-message error"
        }
        resultDiv.style.display = "block"
      }
    })
    .catch((error) => {
      resultDiv.innerText = "❌ Error: " + error.message
    })
}

// =================== MASSIVE INPUT SUBMISSION ===================
async function submitMassiveInput(type) {
  // Get elements
  const serialNumbersEl = document.getElementById(`${type}_serial_numbers_massive`)
  const qcStatusEl = document.getElementById(`${type}_qc_status`)
  const locationEl = document.getElementById(`${type}_location`)
  const resultEl = document.getElementById(`${type}_result_message`)
  const loadingEl = document.getElementById(`${type}_loading_spinner`)

  // Show loading
  loadingEl.style.display = "inline-block"
  resultEl.style.display = "none"

  // Get serial numbers and parse them
  const rawInputs = serialNumbersEl.value
    .split(/[\n\t]+/)
    .map((s) => s.trim())
    .filter((s) => s !== "")

  const dateRegex = /(\d{2}-\d{2}-\d{4})$/ // Matches dd-mm-yyyy at the end of the string

  let processedInputs = []
  if (type === "in") {
    for (const inputLine of rawInputs) {
      const match = inputLine.match(dateRegex)
      if (match) {
        const serialNumber = inputLine.replace(match[0], "").trim()
        // START MODIFIKASI: user_date akan dikirim untuk inv_in
        const userDate = match[0]
        processedInputs.push({ serial_number: serialNumber, user_date: userDate })
        // END MODIFIKASI
      } else {
        processedInputs.push({ serial_number: inputLine }) // SN only
      }
    }
  } else {
    // For 'out' and 'move', only serial number is expected
    processedInputs = rawInputs.map((sn) => ({ serial_number: sn }))
  }

  const url = "http://localhost/cdummy/inventory/input_process" // TETAPKAN URL ASLI ANDA

  let successCount = 0,
    failCount = 0
  const failedSerials = []

  // If no serial numbers, send empty request to get server error message
  const inputsToProcess = processedInputs.length > 0 ? processedInputs : [{ serial_number: "" }]

  // Process each serial number (let server handle all validation)
  for (const item of inputsToProcess) {
    const data = { type, serial_number: item.serial_number }
    if (type === "in") {
      data.qc_status = qcStatusEl?.value
      // START MODIFIKASI: Tambahkan user_date jika ada
      if (item.user_date) {
        data.user_date = item.user_date
      }
      // END MODIFIKASI
    }
    if (type === "move") data.location = locationEl?.value

    try {
      const response = await fetch(url, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      })
      const result = await response.json()

      if (result.success) {
        successCount++
      } else {
        failCount++
        failedSerials.push(`${item.serial_number || "Empty SN"}: ${result.message}`)
      }
    } catch (error) {
      failCount++
      failedSerials.push(`${item.serial_number || "Empty SN"}: ❌ Error: ${error.message}`)
    }
  }

  // Show results
  loadingEl.style.display = "none"
  let message = `Processing complete: ${successCount} successful, ${failCount} failed.`

  if (failCount === 0) {
    serialNumbersEl.value = ""
    refreshCurrentData()
    resultEl.className = "input-result-message success"
  } else {
    message += "\n\nFailed serial numbers:\n" + failedSerials.join("\n")
    // Only keep the serial numbers that failed in the textarea
    serialNumbersEl.value = failedSerials
      .map((f) => f.split(":")[0])
      .filter((sn) => sn !== "Empty SN") // Tambahkan filter ini
      .join("\n")
    resultEl.className = "input-result-message error"
  }

  resultEl.innerText = message
  resultEl.style.display = "block"
}

// =================== REFRESH FUNCTIONS ===================
function refreshCurrentData() {
  const isMainTable =
    (inventoryType === "ECBS" && currentTable === "ecbs") || (inventoryType === "ECCT" && currentTable === "ecct")

  if (isMainTable) {
    showMainData()
  } else {
    showSecondaryData()
  }
}

// Backward compatibility aliases
function refreshCurrentTable() {
  refreshCurrentData()
}

// =================== WINDOW ONLOAD ===================
window.onload = () => {
  if (document.readyState !== "loading") {
    renderToolbar()
    showMainData()
  }
}
