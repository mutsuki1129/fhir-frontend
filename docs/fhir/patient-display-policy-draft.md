# Patient Display Policy Draft

## Purpose

本文件定義 Patient subject 顯示政策。Patient 只作 subject reference / 安全摘要。Patient 不恢復成 Patient management UI。Patient 不顯示完整敏感資料。

## Allowed Current Display

- displayId。
- gender。
- ageRange 或 birthDate=masked。
- patientReference。

## Default Prohibited Display

禁止預設顯示完整姓名、完整生日、地址、電話、身分證、完整 identifier、完整聯絡資訊、完整家屬資訊、未遮罩 PHI。

## Future Expansion Conditions

未來若要擴充 Patient display，必須先完成 role-based access control、minimum necessary principle、audit logging、display masking、consent / authorization review。

## Safety Boundary

Patient 不作 clinical management UI，不作 Patient write UI，不作完整 PHI display。Read-only Lesion Viewer 僅顯示必要 subject context。
