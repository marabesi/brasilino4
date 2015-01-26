import java.awt.Robot;
import java.awt.event.InputEvent;
import java.awt.event.KeyEvent;
import java.io.BufferedReader;
import java.io.BufferedWriter;
import java.io.InputStreamReader;
import java.io.InputStream;
import java.io.OutputStreamWriter;
import java.net.ServerSocket;
import java.net.Socket;
import com.pi4j.io.gpio.GpioController;
import com.pi4j.io.gpio.GpioFactory;
import com.pi4j.io.gpio.GpioPinDigitalOutput;
import com.pi4j.io.gpio.PinState;
import com.pi4j.io.gpio.RaspiPin;

/**
 *
 * @author Ricardo
 */
public class CarrinhoTHT {

    public static void main(String[] args) throws InterruptedException {
	final GpioController gpio = GpioFactory.getInstance();

        final GpioPinDigitalOutput frente = gpio.provisionDigitalOutputPin(RaspiPin.GPIO_15, "MyLED", PinState.HIGH);
	final GpioPinDigitalOutput tras = gpio.provisionDigitalOutputPin(RaspiPin.GPIO_16, "MyLED", PinState.HIGH);
	final GpioPinDigitalOutput direita = gpio.provisionDigitalOutputPin(RaspiPin.GPIO_01, "MyLED", PinState.HIGH);
	final GpioPinDigitalOutput esquerda = gpio.provisionDigitalOutputPin(RaspiPin.GPIO_04, "MyLED", PinState.HIGH);

	OutputStreamWriter output;
        BufferedWriter writer;
        
        BufferedReader leitorLinhas;
        InputStreamReader leitorCaracteres;
        InputStream leitorSocket;
        
        try {
            ServerSocket server = new ServerSocket(8282);
	   System.out.println("Starting socket on port 8282");
            while (true){
		System.out.println("Waiting connection");
                Socket s = server.accept();
                System.out.println(server.getInetAddress());
                
		leitorSocket = s.getInputStream();
		leitorCaracteres = new InputStreamReader(leitorSocket);
		leitorLinhas = new BufferedReader(leitorCaracteres);
		String recebeu = leitorLinhas.readLine();
		
		System.out.println(recebeu);

                switch(recebeu) {

 		case "F": {
                	frente.toggle();
                	break;
                }
		case "T": {
			tras.toggle();
			break;
		}

		case "D": {
			direita.toggle();
			break;
		}
		case "E": {
			esquerda.toggle();
			break;
		}

            }
}
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
        }
    }
}
