tRY
  LOCAL lcSchema, loConfig, loMsg, loError, lcErr
  lcErr = ""
  lcSchema = "http://schemas.microsoft.com/cdo/configuration/"
  loConfig = CREATEOBJECT("CDO.Configuration")
  WITH loConfig.FIELDS
    .ITEM(lcSchema + "smtpserver") = "smtp.gmail.com"
    .ITEM(lcSchema + "smtpserverport") = 465 && � 587
    .ITEM(lcSchema + "sendusing") = 2
    .ITEM(lcSchema + "smtpauthenticate") = .T. 
    .ITEM(lcSchema + "smtpusessl") = .T.
    .ITEM(lcSchema + "sendusername") = "eparrag@gmail.com"
    .ITEM(lcSchema + "sendpassword") = "3541738"
    .UPDATE
  ENDWITH
  loMsg = CREATEOBJECT ("CDO.Message")
  WITH loMsg
    .Configuration = loConfig
    .FROM = "eparrag@gmail.com"
    .TO = "eparra@ucla.edu.ve"
    .Subject = "Prueba desde Gmail"
    .TextBody = "Este es un mensaje de prueba con CDO con " + ;
      "autenticaci�n y cifrado SSL desde Gmail"
    .Send()
  ENDWITH
CATCH TO loError
  lcErr = [Error: ] + STR(loError.ERRORNO) + CHR(13) + ;
    [Linea: ] + STR(loError.LINENO) + CHR(13) + ;
    [Mensaje: ] + loError.MESSAGE
FINALLY
  RELEASE loConfig, loMsg
  STORE .NULL. TO loConfig, loMsg
  IF EMPTY(lcErr)
    MESSAGEBOX("El mensaje se envi� con �xito", 64, "Aviso")
  ELSE
    MESSAGEBOX(lcErr, 16 , "Error")
  ENDIF
ENDTRY


*o=createobject("outlook.application") 
*oitem=o.createitem(0) 
*oitem.subject="Servicios Medicos Informa" 
*oitem.to="eparra@ucla.edu.ve" 
*oitem.body="En el cuerpo del correo" 
*oitem.Attachments.Add("c:\proyectovfp\determinar2.prg")
*oitem.send 
*KEYBOARD CHR(13)
*o=.null. 
