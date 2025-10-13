package com.example.matute.android;

// ------------------------------------------------------------------
// Gestión de la actividad principal: escaneo, detección y envío BLE
// ------------------------------------------------------------------

import android.Manifest;
import android.annotation.SuppressLint;
import android.bluetooth.BluetoothAdapter;
import android.bluetooth.BluetoothDevice;
import android.bluetooth.le.BluetoothLeScanner;
import android.bluetooth.le.ScanCallback;
import android.bluetooth.le.ScanFilter;
import android.bluetooth.le.ScanResult;
import android.content.pm.PackageManager;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.List;

public class MainActivity extends AppCompatActivity {

    private static final String ETIQUETA_LOG = ">>>>";
    private static final int CODIGO_PETICION_PERMISOS = 11223344;
    private BluetoothLeScanner elEscanner;
    private ScanCallback callbackDelEscaneo = null;

    private int ultimoNumeroMedicion = -1;

    // --------------------------------------------------------------
    // Escanea todos los dispositivos BLE y los muestra por Logcat
    // --------------------------------------------------------------
    @SuppressLint("MissingPermission")
    private void buscarTodosLosDispositivosBTLE() {
        Log.d(ETIQUETA_LOG, "buscarTodosLosDispositivosBTLE(): empieza");

        this.callbackDelEscaneo = new ScanCallback() {
            @Override
            public void onScanResult(int callbackType, ScanResult resultado) {
                super.onScanResult(callbackType, resultado);
                mostrarInformacionDispositivoBTLE(resultado);
            }
        };

        this.elEscanner.startScan(this.callbackDelEscaneo);
        Log.d(ETIQUETA_LOG, "buscarTodosLosDispositivosBTLE(): escaneando...");
    }

    // --------------------------------------------------------------
    // Muestra por Logcat la información de cada dispositivo detectado
    // --------------------------------------------------------------
    @SuppressLint("MissingPermission")
    private void mostrarInformacionDispositivoBTLE(ScanResult resultado) {
        BluetoothDevice bluetoothDevice = resultado.getDevice();
        byte[] bytes = resultado.getScanRecord().getBytes();

        Log.d(ETIQUETA_LOG, "****************************************************");
        Log.d(ETIQUETA_LOG, "DISPOSITIVO DETECTADO:");
        Log.d(ETIQUETA_LOG, "Nombre: " + bluetoothDevice.getName());
        Log.d(ETIQUETA_LOG, "Dirección: " + bluetoothDevice.getAddress());
        Log.d(ETIQUETA_LOG, "****************************************************");

        TramaIBeacon tib = new TramaIBeacon(bytes);
        String nombre = bluetoothDevice.getName();

        // Si es nuestro dispositivo (placa BLE)
        if (nombre != null && nombre.equalsIgnoreCase("prueba21")) {
            int major = Utilidades.bytesToInt(tib.getMajor());
            int minor = Utilidades.bytesToInt(tib.getMinor());

            // major codifica tipo_medicion y número de medición
            int tipo_medicion = (major >> 8);       // parte alta → tipo (11 = CO2, 12 = temp)
            int numero_medicion = (major & 0xFF);   // parte baja → contador

            Log.d(ETIQUETA_LOG, ">>> Beacon nuestro detectado <<<");
            if (numero_medicion != ultimoNumeroMedicion) {
                ultimoNumeroMedicion = numero_medicion;
                enviarMedicionAlServidor(tipo_medicion, minor, numero_medicion, nombre);
                Toast.makeText(this, "Nueva medición enviada (" + numero_medicion + ")", Toast.LENGTH_SHORT).show();
            } else {
                Log.d(ETIQUETA_LOG, "Número repetido (" + numero_medicion + "), no se envía.");
            }
        }
    }

    // --------------------------------------------------------------
    // Envía los datos (tipo, valor, contador, nombre) al backend PHP
    // --------------------------------------------------------------
    private void enviarMedicionAlServidor(int tipoMedicion, int medicion, int numeroMedicion, String dispositivoId) {
        new Thread(() -> {
            try {
                URL url = new URL("https://ematbla.upv.edu.es/api/post_mediciones.php");
                HttpURLConnection con = (HttpURLConnection) url.openConnection();

                con.setRequestMethod("POST");
                con.setRequestProperty("Content-Type", "application/json; utf-8");
                con.setDoOutput(true);

                String jsonInputString = "{"
                        + "\"tipo_medicion\": " + tipoMedicion + ", "
                        + "\"medicion\": " + medicion + ", "
                        + "\"numero_medicion\": " + numeroMedicion + ", "
                        + "\"dispositivo_id\": \"" + dispositivoId + "\"}";

                Log.d(ETIQUETA_LOG, "JSON enviado: " + jsonInputString);

                try (OutputStream os = con.getOutputStream()) {
                    byte[] input = jsonInputString.getBytes("utf-8");
                    os.write(input, 0, input.length);
                }

                int code = con.getResponseCode();
                Log.d(ETIQUETA_LOG, "Respuesta del servidor: " + code);

                try (BufferedReader br = new BufferedReader(new InputStreamReader(con.getInputStream(), "utf-8"))) {
                    StringBuilder response = new StringBuilder();
                    String responseLine;
                    while ((responseLine = br.readLine()) != null) {
                        response.append(responseLine.trim());
                    }
                    Log.d(ETIQUETA_LOG, "Respuesta del backend: " + response.toString());
                }

                con.disconnect();

            } catch (Exception e) {
                Log.e(ETIQUETA_LOG, "Error al enviar medición: " + e.getMessage());
            }
        }).start();
    }

    // --------------------------------------------------------------
    // Busca específicamente nuestro beacon BLE
    // --------------------------------------------------------------
    @SuppressLint("MissingPermission")
    private void buscarEsteDispositivoBTLE(final String dispositivoBuscado) {
        Log.d(ETIQUETA_LOG, "buscarEsteDispositivoBTLE(): empieza");

        this.callbackDelEscaneo = new ScanCallback() {
            @Override
            public void onScanResult(int callbackType, ScanResult resultado) {
                super.onScanResult(callbackType, resultado);
                BluetoothDevice bluetoothDevice = resultado.getDevice();
                String nombre = bluetoothDevice.getName();

                Log.d(ETIQUETA_LOG, "buscarEsteDispositivoBTLE(): onScanResult(), detectado: " + nombre);

                if (nombre == null || !nombre.equalsIgnoreCase(dispositivoBuscado)) return;
                mostrarInformacionDispositivoBTLE(resultado);
            }
        };

        this.elEscanner.startScan(this.callbackDelEscaneo);
        Log.d(ETIQUETA_LOG, "Escaneando en busca de: " + dispositivoBuscado);
    }

    // --------------------------------------------------------------
    // Detiene el escaneo BLE
    // --------------------------------------------------------------
    @SuppressLint("MissingPermission")
    private void detenerBusquedaDispositivosBTLE() {
        if (this.callbackDelEscaneo == null) return;
        this.elEscanner.stopScan(this.callbackDelEscaneo);
        this.callbackDelEscaneo = null;
        Log.d(ETIQUETA_LOG, "Escaneo detenido.");
    }

    // --------------------------------------------------------------
    // Botones UI
    // --------------------------------------------------------------
    public void botonBuscarDispositivosBTLEPulsado(View v) {
        Log.d(ETIQUETA_LOG, "Botón: buscar todos los dispositivos");
        this.buscarTodosLosDispositivosBTLE();
    }

    public void botonBuscarNuestroDispositivoBTLEPulsado(View v) {
        Log.d(ETIQUETA_LOG, "Botón: buscar NUESTRO dispositivo");
        this.buscarEsteDispositivoBTLE("prueba21");
    }

    public void botonDetenerBusquedaDispositivosBTLEPulsado(View v) {
        Log.d(ETIQUETA_LOG, "Botón: detener búsqueda");
        this.detenerBusquedaDispositivosBTLE();
    }

    // --------------------------------------------------------------
    // Inicializa Bluetooth y permisos
    // --------------------------------------------------------------
    @SuppressLint("MissingPermission")
    private void inicializarBlueTooth() {
        BluetoothAdapter bta = BluetoothAdapter.getDefaultAdapter();
        bta.enable();

        this.elEscanner = bta.getBluetoothLeScanner();

        if (this.elEscanner == null) {
            Log.e(ETIQUETA_LOG, "ERROR: No se obtuvo escáner BLE");
        }

        if (
                ContextCompat.checkSelfPermission(this, Manifest.permission.BLUETOOTH) != PackageManager.PERMISSION_GRANTED
                        || ContextCompat.checkSelfPermission(this, Manifest.permission.BLUETOOTH_ADMIN) != PackageManager.PERMISSION_GRANTED
                        || ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) != PackageManager.PERMISSION_GRANTED
        ) {
            ActivityCompat.requestPermissions(
                    MainActivity.this,
                    new String[]{Manifest.permission.BLUETOOTH, Manifest.permission.BLUETOOTH_ADMIN, Manifest.permission.ACCESS_FINE_LOCATION},
                    CODIGO_PETICION_PERMISOS
            );
        } else {
            Log.d(ETIQUETA_LOG, "Permisos Bluetooth concedidos.");
        }
    }

    // --------------------------------------------------------------
    // onCreate principal
    // --------------------------------------------------------------
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        Log.d(ETIQUETA_LOG, "onCreate(): inicializando Bluetooth...");
        inicializarBlueTooth();
        Log.d(ETIQUETA_LOG, "onCreate(): listo.");
    }

    // --------------------------------------------------------------
    // Resultado de solicitud de permisos
    // --------------------------------------------------------------
    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == CODIGO_PETICION_PERMISOS) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED)
                Log.d(ETIQUETA_LOG, "Permisos concedidos");
            else
                Log.e(ETIQUETA_LOG, "Permisos DENEGADOS");
        }
    }
}
