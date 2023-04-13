<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields =$request->validate([
            'email' => 'required|string|unique:users,email',
            'razonSocial' => 'required|string',
            'telefono' => 'required|string',
            'localidad' => 'required|string',
            'password' => 'required|string|confirmed',
        ]);

        $user = User::create([
            'email' => $fields['email'],
            'razonSocial' => $fields['razonSocial'],
            'telefono' => $fields['telefono'],
            'localidad' => $fields['localidad'],
            'password' => bcrypt($fields['password']),
        ]);

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response,201);

    }

    public function login(Request $request) {
        $fields =$request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        //check email
        $user = User::where('email', $fields['email'])->first();

        //check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Datos erroneos, por favor verifique'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response,201);

    }

    public function logout(Request $request) {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logout'
        ];
    }

    public function getUserRole() {
        $userRole = auth()->user()->getRoleNames()->first();
        return [$userRole];
    }

    public function getLista($n) {

        //CODIGO PARA CONECTAR DIRECTAMENTE USANDO GUZZLE
        $endpoint = "https://nube.softwaretempo.com:9569/api/lista-test/$n";
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', $endpoint);

        $statusCode = $response->getStatusCode();
        // $content = $response->getBody();
        $content = json_decode($response->getBody(), true);
        return ($content);


        //CODIGO PARA CONECTAR A TRAVES DE PROXY
        // $client = new \GuzzleHttp\Client();
        
        // $endpoint = "https://nube.softwaretempo.com:9569/api/lista-test/$n";
        // $URI = "https://us11.proxysite.com/includes/process.php?action=update";
        // //TEST
        
        // $curl = curl_init();

        // curl_setopt_array($curl, array(
        //   CURLOPT_URL => 'https://us11.proxysite.com/includes/process.php?action=update',
        //   CURLOPT_RETURNTRANSFER => true,
        //   CURLOPT_ENCODING => '',
        //   CURLOPT_MAXREDIRS => 10,
        //   CURLOPT_TIMEOUT => 0,
        //   CURLOPT_FOLLOWLOCATION => true,
        //   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //   CURLOPT_CUSTOMREQUEST => 'POST',
        //   CURLOPT_POSTFIELDS => 'd=https%3A%2F%2Fnube.softwaretempo.com%3A9569%2Fapi%2Flista-test%2F'.$n,
        //   CURLOPT_HTTPHEADER => array(
        //     'Content-Type: application/x-www-form-urlencoded',
        //     'Cookie: PHPSESSID=lfltg1qbm2tkv8f8slqg0prgb1'
        //   ),
        // ));
        
        // $response = json_decode(curl_exec($curl),true);
        
        // curl_close($curl);
        // return $response;
        
    }

    public function createSpreadSheet($arr) {
        
        $listContent = [];

        $nameList = (object) [
            '1' => 'Publico', 
            '2' => 'Por Mayor',
            '3' => 'Distribuidor C',
            '5' => 'Distribuidor Zonal',
            '6' => 'Distribuidor M',
            '7' => 'Publico por Mayor',
            '8' => 'Franquicias',
            '13' => 'Franquicias Int',
            '14' => 'Distribuidor Norte',
        ];
        try {
            $spreadsheet = new Spreadsheet();
            for($i = 0; $i < count($arr); $i++){
                $listContent[$i] = AuthController::getLista($arr[$i]);
                $spreadsheet->createSheet();
                $sheet = $spreadsheet->setActiveSheetIndex($i);
                $spreadsheet->getActiveSheet()->setTitle($nameList->{$arr[$i]});
                $spreadsheet->getActiveSheet()->mergeCells("A1:E5");
                $spreadsheet->getActiveSheet()->getStyle('A1:E5')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF2957a4');
                $spreadsheet->getActiveSheet()->getStyle('A1:E5')->getFont()
                    ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $spreadsheet->getActiveSheet()->getStyle('A1:E5')->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('A1:E5')->getFont()->setSize(12);
                $spreadsheet->getActiveSheet()->getStyle('A1:E5')->getFont()->setName('MS Sans Serif');
                $sheet->getStyle('A1')->getAlignment()->setVertical('center');
                $sheet->getStyle('A1')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('A1')->getAlignment()->setIndent(33);
                $sheet->setCellValue('A1', "Lista de Precios - ".$nameList->{$arr[$i]}." ".date("d/m/Y"));
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('azulina');
                $drawing->setDescription('azulina');
                $drawing->setPath(__DIR__ .'\azulina.png');
                $drawing->setCoordinates('A1');
                $drawing->setHeight(85);
                $drawing->setOffsetX(10);
                $drawing->setWorksheet($spreadsheet->getActiveSheet());
                
                $spreadsheet->getActiveSheet()->getStyle('A7:E7')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FF2957a4');
                $spreadsheet->getActiveSheet()->getStyle('A7:E7')->getFont()
                    ->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
                $spreadsheet->getActiveSheet()->getStyle('A7:E7')->getFont()->setBold(true);
                $sheet->getStyle('A7:E7')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A')->getAlignment()->setHorizontal('left');
                $sheet->getStyle('C')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('D')->getAlignment()->setHorizontal('right');
                $sheet->getStyle('E')->getAlignment()->setHorizontal('right');
                $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(16.98, 'px');
                $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(35.88, 'px');
                $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(16.72, 'px');
                $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(10.94, 'px');
                $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(13.24, 'px');
                $sheet->setCellValue('A7', "Codigo Producto");
                $sheet->setCellValue('B7',"Descripcion");
                $sheet->setCellValue('C7', "Precio Vta s/IVA");
                $sheet->setCellValue('D7', "IVA");
                $sheet->setCellValue('E7', "Precio Final");
                for($j = 0; $j < count($listContent[$i]); $j++) {
                    $sheet->setCellValue('A'.$j+8, $listContent[$i][$j]["ProductoId"]);
                    $sheet->setCellValue('B'.$j+8, $listContent[$i][$j]["Descripcion"]);
                    $sheet->setCellValue('C'.$j+8, '$'.$listContent[$i][$j]["Precio"]);
                    $sheet->setCellValue('D'.$j+8, $listContent[$i][$j]["IVA"].'%');
                    $sheet->setCellValue('E'.$j+8, '$'.$listContent[$i][$j]["precioFinal"]);
                }
            }
            $sheetIndex = $spreadsheet->getIndex(
                $spreadsheet->getSheetByName('Worksheet 1')
            );
            $spreadsheet->removeSheetByIndex($sheetIndex);
    
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="myfile.xlsx"');
            header('Access-Control-Allow-Origin: *');
            header('Cache-Control: max-age=0');
    
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
        } catch (\Exception $e) {
            throw new HttpException(500, $e->getMessage());
        }

    }

    public function listaprecios (Request $request) {
        
        $publico = 1;
        $porMayor = 2;
        $distribuidorC = 3;
        $distribuidorZonal = 5;
        $distribuidorM = 6;
        $publicoXmayor = 7;
        $franquicias = 8;
        $franquiciasInt = 13;
        $distribuidorNorte = 14;

        $userRole = auth()->user()->getRoleNames()->first();

        switch ($userRole) {
            case 'Admin':
                return AuthController::createSpreadSheet(
                    [
                        $publico,
                        $porMayor,
                        $distribuidorC,
                        $distribuidorZonal,
                        $distribuidorM,
                        $publicoXmayor,
                        $franquicias,
                        $franquiciasInt,
                        $distribuidorNorte
                    ]);
                break;
            case 'Local Propio':
                return AuthController::createSpreadSheet(
                    [
                        $publico,
                        $publicoXmayor,
                        $porMayor
                    ]);
                break;
            case 'Franquicias 1':
                return AuthController::createSpreadSheet(
                    [
                        $franquicias,
                        $publico,
                        $publicoXmayor,
                        $porMayor
                    ]);
                break;
            case 'Franquicias 2':
                return AuthController::createSpreadSheet(
                    [
                        $franquiciasInt,
                        $publico,
                        $publicoXmayor,
                        $porMayor
                    ]);
                break;
            case 'Vendedor 1':
                return AuthController::createSpreadSheet(
                    [
                        $distribuidorNorte
                    ]);
                break;
            case 'Vendedor 2':
                return AuthController::createSpreadSheet(
                    [
                        $distribuidorZonal
                    ]);
                break;
            case 'Vendedor 3':
                return AuthController::createSpreadSheet(
                    [
                        $distribuidorZonal,
                        $porMayor
                    ]);
                break;
            case 'Distribuidor 1':
                return AuthController::createSpreadSheet(
                    [
                        $franquicias
                    ]);
                break;
            case 'Distribuidor 2':
                return AuthController::createSpreadSheet(
                    [
                        $distribuidorZonal
                    ]);
                break;
            case 'Distribuidor 3':
                return AuthController::createSpreadSheet(
                    [
                        $distribuidorM
                    ]);
                break;
            case 'Distribuidor 4':
                return AuthController::createSpreadSheet(
                    [
                        $distribuidorC
                    ]);
                break;
        }

    }
}