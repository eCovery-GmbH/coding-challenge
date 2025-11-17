# 🏋️‍♂️ Laravel Coding Challenge – Training Time Generator

## 🎯 Ziel

Implementiere in Laravel einen Endpunkt, der auf Basis übermittelter Trainingszeiten automatisch Trainings für die nächsten 4 Wochen erstellt und das Training für heute zurückgibt (falls eines existiert).

Die Aufgabe ist so gestaltet, dass sie in ca. 1 Stunde lösbar ist und Bereiche wie Validation, Datum/Zeit, Modeling, Queues und Code-Qualität abdeckt.

---

## 📡 Endpunkt

**POST** `/training-times`

### 📥 Request Body

```json
{
  "training_times": [
    {
      "hours": 16,
      "minutes": 30,
      "weekday": 1
    }
  ]
}
```

### Regeln für `training_times`

* Array mit **0–5 Einträgen**
* Jeder Eintrag:

    * `hours`: Integer, 0–23
    * `minutes`: Integer, 0–59
    * `weekday`: Integer 1–7 (ISO-Woche: 1=Montag, 7=Sonntag)
* Pro Wochentag maximal **ein Trainingseintrag**
* Bei mehreren Einträgen für denselben Wochentag: **Validation-Error**

---

## 🧠 Aufgabe

### 1. Request validieren

* max. 5 Einträge
* gültige Stunden/Minuten
* gültiger Wochentag
* keine doppelten Wochentage

### 2. Trainings für die nächsten 4 Wochen generieren

* Zeitraum: **heute (inkl.) bis heute + 4 Wochen**
* Finde alle passenden Termine für jede Trainingszeit
* Erstelle für jeden Termin ein `Training`-Model:

    * `id`
    * `scheduled_at` (datetime)
    * `created_at` / `updated_at`

### 3. Synchron vs. Asynchron

* Trainings, deren `scheduled_at` **heute** liegt → *synchron* erstellen
* Trainings, die **ab morgen** liegen → *asynchron* via Job dispatchen

### 4. Training für heute zurückgeben

* Falls heute ein Training erstellt wurde → gib dieses Training zurück
* Falls nicht → `"training": null`

---

## 📤 Response Format

### Beispiel, wenn heute ein Training erstellt wurde

```json
{
  "created_today": 1,
  "scheduled_async": 7,
  "training": {
    "id": 12,
    "scheduled_at": "2025-02-14T16:30:00"
  }
}
```

### Wenn heute kein Training existiert

```json
{
  "created_today": 0,
  "scheduled_async": 8,
  "training": null
}
```

---

## 🧱 Vorgaben & Hinweise

Nutze:

* Migration + Model (`Training`)
* Controller
* Form Request für saubere Validation (empfohlen)
* Job für zukünftige Trainings
* Carbon für Datums-/Zeitlogik
* Queue-Driver egal (sync, database, redis)

Fokus liegt auf **Code-Qualität**, nicht auf Edge-Case-Overkill.

---

## 📦 Erwartetes Verhalten (Beispiel)

### Input

```json
{
  "training_times": [
    { "hours": 16, "minutes": 30, "weekday": 1 }
  ]
}
```

Heute ist Montag → Es wird:

* ein Training **für heute** um 16:30 synchron erstellt
* die restlichen Termine (Montage der nächsten 3 Wochen) **asynchron** geplant

### Response

```json
{
  "created_today": 1,
  "scheduled_async": 3,
  "training": {
    "id": 55,
    "scheduled_at": "2025-02-17T16:30:00"
  }
}
```

---

## 📦 Abgabe

Bitte abgeben als:

* Pull Request
* Nutze das gegebene Repo als Basis (klone es und lege direkt los)

### Sicherstellen:

* Migrationen funktionieren
* Der Endpunkt ist testbar
* Optional: kurze How To Test Anleitung in der PR Beschreibung

---

Es ist wichtig, dass du nur das machst, was die Coding Challenge von Dir möchte.
Falls du länger als 2h brauchen solltest, brich bitte ab und wir begutachten den erreichten
Status Quo!

Entwickle so, wie du es auch sonst machen würdest. Nutze alle Tools, etc. wie auch sonst.

Viel Spaß!
