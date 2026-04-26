const endpointUrl = "https://admin.darkdragon888.com/api/lineNotify_tranfer";
const accountNumber = "4201947889";
const accountName = "SCB BOT";
const company = "darkdragon";
const secret = "f399509b-6875-4355-bc83-39738f23bcaf";

let observer;
let mutationTimeout = null;

function setupTitle() {
    document.title = accountNumber;
}

function getFirstSCBMessage() {
    const messageList = document.querySelector(".message_list");
    if (!messageList) {
        console.warn("❌ ไม่พบ .message_list");
        return null;
    }

    const renderers = messageList.querySelectorAll("flex-renderer");

    for (let i = 0; i < renderers.length; i++) {
        const renderer = renderers[i];
        const root = renderer.shadowRoot || renderer;
        const contentEl = root.querySelector(".content");

        if (!contentEl) continue;

        const rawText = contentEl.textContent.trim();
        if (!rawText.includes("รายการเงินเข้า")) continue;

        const amountMatch = rawText.match(/\+([\d,]+\.\d{2})/);
        const nameMatch = rawText.match(/MR\s+([A-Z]+)/i);
        const accMatch = rawText.match(/X-(\d{4})/);
        const datetimeMatch = rawText.match(/(\d{2}\/\d{2}\/\d{4})\s+(\d{2}:\d{2})/);
        const availableMatch = rawText.match(/ยอดเงินที่ใช้ได้([\d,]+\.\d{2})/);

        const amount = amountMatch ? amountMatch[1].replace(/,/g, "") : null;
        const name = nameMatch ? nameMatch[1] : null;
        const account = accMatch ? accMatch[1] : null;

        let date = null, time = null, mysqlFormatDate = null;
        if (datetimeMatch && datetimeMatch[1] && datetimeMatch[2]) {
            date = datetimeMatch[1];
            time = datetimeMatch[2];
            mysqlFormatDate = `${date.split("/").reverse().join("-")} ${time}:00`;
        }

        const available = availableMatch ? availableMatch[1].replace(/,/g, "") : null;

        return {
            amount,
            name,
            account,
            date,
            time,
            sdate: mysqlFormatDate,
            available,
            raw: rawText,
        };
    }

    console.warn("❌ ไม่พบข้อความ 'รายการเงินเข้า'");
    return null;
}


function initObserver() {
    observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (!(node instanceof HTMLElement)) continue;
                if (node.querySelector && node.querySelector("flex-renderer")) {
                    if (mutationTimeout) clearTimeout(mutationTimeout);
                    mutationTimeout = setTimeout(() => {
                        const data = getFirstSCBMessage();
                        if (!data) return;

                        console.log("✅ ข้อมูลที่จะส่ง:", data);

                        const body = {
                            amount: data.amount,
                            acc_no: data.account,
                            acc_name: data.name,
                        };

                        // Uncomment เมื่อพร้อมส่ง

                        fetch(endpointUrl, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-api-key": secret,
                            },
                            body: JSON.stringify(body),
                        })
                            .then(() => console.log("🚀 ส่งข้อมูลเรียบร้อย"))
                            .catch((err) => console.error("❌ เกิดข้อผิดพลาด:", err));

                    }, 300); // debounce
                }
            }
        }
    });
}

function waitForMessageListThenObserve() {
    const tryFind = () => {
        const msgList = document.querySelector(".message_list");
        if (msgList) {
            console.log("✅ พบ .message_list แล้ว เริ่ม observer");
            observer.observe(msgList, { childList: true, subtree: true });
        } else {
            console.log("⏳ รอ .message_list...");
            setTimeout(tryFind, 500);
        }
    };
    tryFind();
}

function stopObserve() {
    if (observer) {
        observer.disconnect();
        console.log("⏹️ หยุดเฝ้าดูเรียบร้อย");
    }
}

function testServer() {
    const body = {
        amount: '1123.96',
        acc_no: '0000',
        acc_name: 'test',
    };
    fetch(endpointUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-api-key": secret,
        },
        body: JSON.stringify(body),
    })
        .then(() => console.log("🚀testServer ส่งข้อมูลเรียบร้อย"))
        .catch((err) => console.error("❌ เกิดข้อผิดพลาด:", err));
}

// ✅ เริ่มทำงานเมื่อ DOM โหลดเสร็จ

testServer();
// setupTitle();
initObserver();
waitForMessageListThenObserve();

